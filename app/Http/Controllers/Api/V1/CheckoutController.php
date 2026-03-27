<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\PaymentConfirmed;
use App\Http\Resources\OrderResource;
use App\Models\Coupon;
use App\Models\Order;
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
                'items'   => \App\Http\Resources\CartResource::collection($cart['items']),
                'totals'  => $cart['totals'],
                'coupon'  => $cart['coupon']?->code,
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

        try {
            DB::beginTransaction();

            $order = $this->orderService->createFromCart(
                $user,
                $items,
                $data['shipping_address'],
                $billingAddress,
                $coupon,
                'stripe',
                $data['notes'] ?? null,
            );

            // Créer le PaymentIntent Stripe
            $paymentIntent = $this->createStripePaymentIntent($order);

            $order->update(['payment_reference' => $paymentIntent['id']]);

            $this->cartService->clear($user);

            DB::commit();

            return $this->success([
                'order'                => new OrderResource($order->load('items')),
                'client_secret'        => $paymentIntent['client_secret'],
                'stripe_publishable_key' => config('services.stripe.key'),
            ], 'Commande créée. Procédez au paiement.', 201);

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

    private function createStripePaymentIntent(Order $order): array
    {
        if (! class_exists(\Stripe\StripeClient::class)) {
            // Stripe non installé — retourner un stub pour le développement
            return ['id' => 'pi_dev_' . $order->id, 'client_secret' => 'dev_secret'];
        }

        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        $intent = $stripe->paymentIntents->create([
            'amount'   => (int) round($order->total * 100),
            'currency' => 'eur',
            'metadata' => ['order_id' => $order->id, 'order_number' => $order->order_number],
        ]);

        return ['id' => $intent->id, 'client_secret' => $intent->client_secret];
    }
}
