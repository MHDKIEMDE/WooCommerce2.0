<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code'        => 'BIENVENUE10',
                'type'        => 'percent',
                'value'       => 10,
                'min_order'   => 15000,   // 15 000 FCFA
                'usage_limit' => null,
                'is_active'   => true,
                'expires_at'  => now()->addYear(),
            ],
            [
                'code'        => 'ETE2026',
                'type'        => 'percent',
                'value'       => 15,
                'min_order'   => 25000,   // 25 000 FCFA
                'usage_limit' => 100,
                'is_active'   => true,
                'expires_at'  => '2026-08-31',
            ],
            [
                'code'        => 'PROMO2000',
                'type'        => 'fixed',
                'value'       => 2000,    // 2 000 FCFA
                'min_order'   => 10000,   // 10 000 FCFA
                'usage_limit' => null,
                'is_active'   => true,
                'expires_at'  => null,
            ],
            [
                'code'        => 'LIVGRATUIT',
                'type'        => 'fixed',
                'value'       => 2000,    // Montant livraison standard
                'min_order'   => 0,
                'usage_limit' => 200,
                'is_active'   => true,
                'expires_at'  => now()->addMonths(3),
            ],
        ];

        foreach ($coupons as $data) {
            Coupon::firstOrCreate(['code' => $data['code']], $data);
        }
    }
}
