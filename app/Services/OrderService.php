<?php

namespace App\Services;

use App\Events\OrderPlaced;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Collection;

class OrderService
{
    public function __construct(
        private CouponService $couponService,
    ) {}

    /**
     * Crée une commande pour un sous-ensemble d'articles (une boutique).
     * Appelé une fois par boutique depuis CheckoutController.
     */
    public function createForShop(
        User       $user,
        int        $shopId,
        Collection $items,
        array      $shippingAddress,
        array      $billingAddress,
        ?Coupon    $coupon = null,
        string     $paymentMethod = 'stripe',
        ?string    $notes = null,
    ): Order {
        $subtotal     = $this->calculateSubtotal($items);
        $discount     = $coupon ? $this->couponService->calculateDiscount($coupon, $subtotal) : 0.0;
        $shippingCost = $subtotal >= 50 ? 0.0 : 5.90;
        $taxAmount    = $this->calculateTax($subtotal - $discount);
        $total        = round(($subtotal - $discount) + $shippingCost, 2);

        $order = Order::create([
            'user_id'          => $user->id,
            'shop_id'          => $shopId,
            'order_number'     => $this->generateOrderNumber(),
            'status'           => 'pending',
            'subtotal'         => $subtotal,
            'discount_amount'  => $discount,
            'shipping_cost'    => $shippingCost,
            'tax_amount'       => $taxAmount,
            'total'            => $total,
            'shipping_address' => $shippingAddress,
            'billing_address'  => $billingAddress,
            'payment_method'   => $paymentMethod,
            'payment_status'   => 'pending',
            'coupon_id'        => $coupon?->id,
            'notes'            => $notes,
        ]);

        foreach ($items as $item) {
            $unitPrice = (float) $item->product->price;
            if ($item->variant && $item->variant->price_modifier) {
                $unitPrice += (float) $item->variant->price_modifier;
            }

            $order->items()->create([
                'product_id'       => $item->product_id,
                'variant_id'       => $item->variant_id,
                'product_name'     => $item->product->name,
                'product_sku'      => $item->variant?->sku ?? $item->product->sku,
                'quantity'         => $item->quantity,
                'unit_price'       => $unitPrice,
                'total_price'      => round($unitPrice * $item->quantity, 2),
                'vat_rate'         => $item->product->vat_rate ?? 20,
                'product_snapshot' => [
                    'name'    => $item->product->name,
                    'sku'     => $item->product->sku,
                    'price'   => $item->product->price,
                    'shop_id' => $shopId,
                    'image'   => $item->product->images->first()?->url,
                ],
            ]);
        }

        event(new OrderPlaced($order));

        return $order;
    }

    /**
     * @deprecated Utiliser createForShop() — conservé pour compatibilité boutique #1.
     */
    public function createFromCart(
        User       $user,
        Collection $items,
        array      $shippingAddress,
        array      $billingAddress,
        ?Coupon    $coupon = null,
        string     $paymentMethod = 'stripe',
        ?string    $notes = null,
    ): Order {
        $shopId = $items->first()?->product?->shop_id;
        $order  = $this->createForShop(
            $user, $shopId ?? 0, $items,
            $shippingAddress, $billingAddress,
            $coupon, $paymentMethod, $notes
        );

        if ($coupon) {
            $this->couponService->incrementUsage($coupon);
        }

        return $order;
    }

    public function generateOrderNumber(): string
    {
        $year = now()->year;
        $seq  = str_pad((Order::whereYear('created_at', $year)->count() + 1), 5, '0', STR_PAD_LEFT);

        return "CMD-{$year}-{$seq}";
    }

    public function calculateTax(float $base, float $rate = 0.20): float
    {
        return round($base * $rate, 2);
    }

    private function calculateSubtotal(Collection $items): float
    {
        return round($items->sum(function ($item) {
            $price = (float) $item->product->price;
            if ($item->variant && $item->variant->price_modifier) {
                $price += (float) $item->variant->price_modifier;
            }
            return $price * $item->quantity;
        }), 2);
    }
}
