<?php

namespace App\Services;

use App\Models\Coupon;

class CouponService
{
    public function validate(string $code, float $subtotal): array
    {
        $coupon = Coupon::where('code', strtoupper($code))->first();

        if (! $coupon) {
            return ['valid' => false, 'message' => 'Code promo introuvable.'];
        }

        if (! $coupon->isValid($subtotal)) {
            if (! $coupon->is_active) {
                return ['valid' => false, 'message' => 'Ce code promo est désactivé.'];
            }
            if ($coupon->expires_at && $coupon->expires_at->isPast()) {
                return ['valid' => false, 'message' => 'Ce code promo a expiré.'];
            }
            if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
                return ['valid' => false, 'message' => 'Ce code promo a atteint sa limite d\'utilisation.'];
            }
            if ($coupon->min_order && $subtotal < $coupon->min_order) {
                return [
                    'valid'   => false,
                    'message' => "Montant minimum requis : {$coupon->min_order} €.",
                ];
            }
        }

        $discount = $this->calculateDiscount($coupon, $subtotal);

        return [
            'valid'    => true,
            'coupon'   => $coupon,
            'discount' => $discount,
            'message'  => "Code appliqué : -{$discount} €",
        ];
    }

    public function calculateDiscount(Coupon $coupon, float $subtotal): float
    {
        if ($coupon->type === 'percent') {
            return round($subtotal * ($coupon->value / 100), 2);
        }

        // fixed
        return min((float) $coupon->value, $subtotal);
    }

    public function incrementUsage(Coupon $coupon): void
    {
        $coupon->increment('used_count');
    }
}
