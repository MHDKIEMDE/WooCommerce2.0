<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Boutique
            ['key' => 'shop_name',        'value' => 'Agri-Shop',                    'group' => 'shop'],
            ['key' => 'shop_email',       'value' => 'contact@agri-shop.fr',         'group' => 'shop'],
            ['key' => 'shop_phone',       'value' => '01 23 45 67 89',               'group' => 'shop'],
            ['key' => 'shop_address',     'value' => '12 Rue du Marché, 75001 Paris','group' => 'shop'],
            ['key' => 'shop_currency',    'value' => 'EUR',                          'group' => 'shop'],
            ['key' => 'shop_locale',      'value' => 'fr_FR',                        'group' => 'shop'],

            // Livraison
            ['key' => 'shipping_free_threshold', 'value' => '50',   'group' => 'shipping'],
            ['key' => 'shipping_cost',           'value' => '5.90', 'group' => 'shipping'],
            ['key' => 'shipping_carrier',        'value' => 'Colissimo', 'group' => 'shipping'],

            // Taxes
            ['key' => 'vat_default',      'value' => '20',  'group' => 'tax'],
            ['key' => 'vat_food',         'value' => '5.5', 'group' => 'tax'],
            ['key' => 'vat_drinks',       'value' => '5.5', 'group' => 'tax'],

            // Paiement
            ['key' => 'stripe_enabled',   'value' => 'true',  'group' => 'payment'],
            ['key' => 'payment_methods',  'value' => 'stripe', 'group' => 'payment'],

            // Stock
            ['key' => 'low_stock_threshold', 'value' => '5', 'group' => 'stock'],
            ['key' => 'stock_alert_email',   'value' => 'admin@agri-shop.fr', 'group' => 'stock'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'group' => $setting['group']]
            );
        }
    }
}
