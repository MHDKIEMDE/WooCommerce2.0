<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService     $cartService,
        private WhatsAppService $whatsApp,
    ) {}

    public function show(Request $request): View|RedirectResponse
    {
        $cart = $this->cartService->getCart($request->user());

        if ($cart['count'] === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Votre panier est vide.');
        }

        return view('checkout', [
            'items'  => $cart['items'],
            'totals' => $cart['totals'],
            'coupon' => $cart['coupon'],
            'user'   => $request->user(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $cart = $this->cartService->getCart($request->user());

        if ($cart['count'] === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Votre panier est vide.');
        }

        $data = $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'email'          => 'required|email|max:150',
            'phone'          => 'required|string|max:30',
            'address'        => 'required|string|max:255',
            'city'           => 'required|string|max:100',
            'country'        => 'required|string|max:100',
            'postal_code'    => 'nullable|string|max:20',
            'payment_method' => 'required|in:cash_on_delivery,bank_transfer',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $totals  = $cart['totals'];
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

        $order = DB::transaction(function () use ($data, $cart, $totals, $address, $request) {
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
                'payment_status'  => 'pending',
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

        return redirect()->route('checkout.confirmation', $order->order_number)
            ->with('success', 'Commande passée avec succès !');
    }

    public function confirmation(string $orderNumber): View|RedirectResponse
    {
        $order = Order::with('items')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('order-confirmation', compact('order'));
    }
}
