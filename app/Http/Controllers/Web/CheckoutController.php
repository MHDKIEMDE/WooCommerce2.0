<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService     $cartService,
        private WhatsAppService $whatsApp,
    ) {}

    /**
     * Crée un PaymentIntent Stripe et retourne le client_secret au JS.
     */
    public function createStripeIntent(Request $request): JsonResponse
    {
        $cart = $this->cartService->getCart($request->user());

        if ($cart['count'] === 0) {
            return response()->json(['error' => 'Panier vide.'], 422);
        }

        if (! class_exists(\Stripe\StripeClient::class)) {
            return response()->json(['error' => 'Stripe non installé.'], 500);
        }

        $totals  = $this->cartService->calculateTotals($cart['items'], $cart['coupon'], 0);
        $amount  = (int) round($totals['total'] * 100); // centimes

        try {
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
            $intent = $stripe->paymentIntents->create([
                'amount'   => max($amount, 50),
                'currency' => 'xof',
                'automatic_payment_methods' => ['enabled' => true],
            ]);

            return response()->json(['client_secret' => $intent->client_secret]);
        } catch (\Throwable $e) {
            Log::error('Stripe intent error: ' . $e->getMessage());
            return response()->json(['error' => 'Impossible de contacter Stripe.'], 500);
        }
    }

    public function show(Request $request): View|RedirectResponse
    {
        $cart = $this->cartService->getCart($request->user());

        if ($cart['count'] === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Votre panier est vide.');
        }

        $zones = DeliveryZone::active()->get();

        return view('checkout', [
            'items'  => $cart['items'],
            'totals' => $cart['totals'],
            'coupon' => $cart['coupon'],
            'user'   => $request->user(),
            'zones'  => $zones,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $cart = $this->cartService->getCart($request->user());

        if ($cart['count'] === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Votre panier est vide.');
        }

        $zones = DeliveryZone::active()->get();

        $data = $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'email'          => 'nullable|email|max:150',
            'phone'          => 'required|string|max:30',
            'address'        => 'required|string|max:255',
            'city'           => 'required|string|max:100',
            'country'        => 'nullable|string|max:100',
            'postal_code'    => 'nullable|string|max:20',
            'payment_method'              => 'required|in:cash_on_delivery,bank_transfer,stripe',
            'stripe_payment_intent_id'    => 'required_if:payment_method,stripe|nullable|string',
            'notes'          => 'nullable|string|max:1000',
            'zone_id'        => $zones->isNotEmpty() ? 'required|exists:delivery_zones,id' : 'nullable',
        ]);

        // Calcul du frais de livraison selon la zone choisie
        $zone         = $zones->isNotEmpty() && ! empty($data['zone_id'])
            ? $zones->firstWhere('id', $data['zone_id'])
            : null;
        $shippingCost = $zone ? (float) $zone->price : 0.0;

        // L'adresse = nom de la zone choisie
        if ($zone) {
            $data['address'] = $zone->name;
        }

        $totals = $this->cartService->calculateTotals($cart['items'], $cart['coupon'], $shippingCost);
        $address = [
            'first_name'  => $data['first_name'],
            'last_name'   => $data['last_name'],
            'email'       => $data['email'],
            'phone'       => $data['phone'],
            'address'     => $data['address'],
            'city'        => $data['city'],
            'country'     => $data['country'],
            'postal_code' => $data['postal_code'] ?? '',
        ];

        // Vérifier le PaymentIntent Stripe avant de créer la commande
        $stripeVerified = false;
        if ($data['payment_method'] === 'stripe') {
            $intentId = $data['stripe_payment_intent_id'] ?? null;
            if (! $intentId || ! class_exists(\Stripe\StripeClient::class)) {
                return back()->withErrors(['payment_method' => 'Paiement Stripe invalide.']);
            }
            try {
                $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
                $intent = $stripe->paymentIntents->retrieve($intentId);
                if ($intent->status !== 'succeeded') {
                    return back()->withErrors(['payment_method' => 'Le paiement n\'a pas été confirmé.']);
                }
                $stripeVerified = true;
            } catch (\Throwable $e) {
                Log::error('Stripe verify error: ' . $e->getMessage());
                return back()->withErrors(['payment_method' => 'Impossible de vérifier le paiement Stripe.']);
            }
        }

        $order = DB::transaction(function () use ($data, $cart, $totals, $address, $request, $stripeVerified) {
            $order = Order::create([
                'user_id'         => $request->user()?->id,
                'order_number'    => 'CMD-' . strtoupper(Str::random(8)),
                'status'          => 'pending',
                'subtotal'        => $totals['subtotal'],
                'shipping_cost'   => $totals['shippingCost'],
                'tax_amount'      => $totals['taxAmount'],
                'discount_amount' => $totals['discount'],
                'total'           => $totals['total'],
                'billing_address' => $address,
                'shipping_address'=> $address,
                'payment_method'  => $data['payment_method'],
                'payment_status'  => $stripeVerified ? 'paid' : 'pending',
                'coupon_id'       => $cart['coupon']?->id,
                'notes'           => $data['notes'] ?? null,
            ]);

            foreach ($cart['items'] as $item) {
                $product   = $item->product;
                $unitPrice = $product->price + ($item->variant?->price_modifier ?? 0);

                OrderItem::create([
                    'order_id'         => $order->id,
                    'product_id'       => $product->id,
                    'variant_id'       => $item->variant_id,
                    'product_name'     => $product->name,
                    'product_sku'      => $product->sku,
                    'quantity'         => $item->quantity,
                    'unit_price'       => $unitPrice,
                    'total_price'      => round($unitPrice * $item->quantity, 2),
                    'vat_rate'         => $product->vat_rate ?? 20,
                    'product_snapshot' => [
                        'name'  => $product->name,
                        'slug'  => $product->slug,
                        'price' => $unitPrice,
                        'image' => $product->images->first()?->url,
                    ],
                ]);
            }

            $this->cartService->clear($request->user());

            return $order;
        });

        // Notification WhatsApp au propriétaire (non-bloquant)
        try {
            $this->whatsApp->notifyNewOrder($order->load('items'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('WhatsApp notification failed: ' . $e->getMessage());
        }

        session(['confirmed_order' => $order->order_number]);

        return redirect()->route('checkout.confirmation', $order->order_number)
            ->with('success', 'Commande passée avec succès !');
    }

    public function confirmation(Request $request, string $orderNumber): View|RedirectResponse
    {
        $order = Order::with('items')
            ->where('order_number', $orderNumber)
            ->first();

        if (! $order) {
            return redirect()->route('home')
                ->with('error', 'Commande introuvable.');
        }

        // Accès autorisé si :
        // 1) l'utilisateur connecté est propriétaire de la commande
        // 2) ou la commande vient d'être passée dans cette session (invité)
        $isOwner      = $request->user() && $request->user()->id === $order->user_id;
        $isJustPlaced = session('confirmed_order') === $orderNumber;

        if (! $isOwner && ! $isJustPlaced) {
            if ($request->user()) {
                return redirect()->route('account.orders')
                    ->with('error', 'Vous n\'êtes pas autorisé à accéder à cette commande.');
            }
            return redirect()->route('home')
                ->with('error', 'Accès non autorisé. Veuillez vous connecter pour consulter vos commandes.');
        }

        // On conserve la clé de session pour permettre le rechargement de la page de confirmation
        // mais on la supprime après 1 affichage via flash
        $request->session()->keep(['confirmed_order']);

        return view('order-confirmation', compact('order'));
    }
}
