<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\PaymentConfirmed;
use App\Http\Resources\OrderResource;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Shop;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends BaseApiController
{
    public function __construct(
        private CartService  $cartService,
        private OrderService $orderService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $cart = $this->cartService->getCart($request->user());

        if ($cart['items']->isEmpty()) {
            return $this->error('Votre panier est vide.', 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => [
                'by_shop'    => $cart['by_shop'],
                'items'      => \App\Http\Resources\CartResource::collection($cart['items']),
                'totals'     => $cart['totals'],
                'coupon'     => $cart['coupon']?->code,
                'shop_count' => $cart['shop_count'],
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'shipping_address'            => ['required', 'array'],
            'shipping_address.first_name' => ['required', 'string'],
            'shipping_address.last_name'  => ['required', 'string'],
            'shipping_address.street'     => ['required', 'string'],
            'shipping_address.city'       => ['required', 'string'],
            'shipping_address.zip'        => ['required', 'string'],
            'shipping_address.country'    => ['required', 'string'],
            'billing_address'             => ['nullable', 'array'],
            'notes'                       => ['nullable', 'string', 'max:500'],
        ]);

        $user  = $request->user();
        $items = $this->cartService->getItems($user);

        if ($items->isEmpty()) {
            return $this->error('Votre panier est vide.', 422);
        }

        $couponCode = session('cart_coupon');
        $coupon     = $couponCode ? Coupon::where('code', $couponCode)->first() : null;

        $billingAddress = $data['billing_address'] ?? $data['shipping_address'];

        // Grouper les articles par boutique
        $byShop = $items->groupBy(fn ($item) => $item->product?->shop_id ?? 0);

        try {
            DB::beginTransaction();

            $results = [];

            foreach ($byShop as $shopId => $shopItems) {
                $order = $this->orderService->createForShop(
                    $user,
                    (int) $shopId,
                    $shopItems,
                    $data['shipping_address'],
                    $billingAddress,
                    $coupon,
                    'stripe',
                    $data['notes'] ?? null,
                );

                $shop          = Shop::find($shopId);
                $paymentIntent = $this->createStripePaymentIntent($order, $shop);

                $order->update(['payment_reference' => $paymentIntent['id']]);

                $results[] = [
                    'order'         => new OrderResource($order->load('items')),
                    'client_secret' => $paymentIntent['client_secret'],
                    'shop'          => $shop ? ['name' => $shop->name, 'slug' => $shop->slug] : null,
                ];
            }

            if ($coupon) {
                app(\App\Services\CouponService::class)->incrementUsage($coupon);
            }

            $this->cartService->clear($user);

            DB::commit();

            return $this->success([
                'orders'                 => $results,
                'stripe_publishable_key' => config('services.stripe.key'),
                'order_count'            => count($results),
            ], count($results) > 1
                ? count($results) . ' commandes créées (une par boutique). Procédez au paiement.'
                : 'Commande créée. Procédez au paiement.',
            201);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Checkout error', ['error' => $e->getMessage()]);
            return $this->error('Erreur lors de la création de la commande.', 500);
        }
    }

    public function webhook(Request $request): JsonResponse
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        if (! $sigHeader || ! $secret) {
            return response()->json(['received' => true]);
        }

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $paymentIntent = $event->data->object;
            $order = Order::where('payment_reference', $paymentIntent->id)->first();

            if ($order && $order->payment_status !== 'paid') {
                $order->update([
                    'payment_status' => 'paid',
                    'status'         => 'processing',
                ]);

                event(new PaymentConfirmed($order));
            }
        }

        return response()->json(['received' => true]);
    }

    private function createStripePaymentIntent(Order $order, ?Shop $shop = null): array
    {
        if (! class_exists(\Stripe\StripeClient::class)) {
            return ['id' => 'pi_dev_' . $order->id, 'client_secret' => 'dev_secret_' . $order->id];
        }

        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        $params = [
            'amount'               => (int) round($order->total * 100),
            'currency'             => 'eur',
            'automatic_payment_methods' => ['enabled' => true],
            'metadata'             => [
                'order_id'     => $order->id,
                'order_number' => $order->order_number,
                'shop_id'      => $shop?->id,
            ],
        ];

        // Stripe Connect : paiement direct vers le compte vendeur
        if ($shop?->stripe_account_id) {
            $commissionRate          = (float) ($shop->commission_rate ?? 0);
            $platformFee             = (int) round($order->total * 100 * $commissionRate / 100);
            $params['transfer_data'] = ['destination' => $shop->stripe_account_id];
            if ($platformFee > 0) {
                $params['application_fee_amount'] = $platformFee;
            }
        }

        $intent = $stripe->paymentIntents->create($params);

        return ['id' => $intent->id, 'client_secret' => $intent->client_secret];
    }
}
