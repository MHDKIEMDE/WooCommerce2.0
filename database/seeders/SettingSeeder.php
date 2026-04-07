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
            ['key' => 'shop_name',     'value' => env('APP_NAME', 'Ma Boutique'),            'group' => 'shop'],
            ['key' => 'shop_tagline',  'value' => 'Bienvenue dans notre boutique en ligne',  'group' => 'shop'],
            ['key' => 'shop_email',    'value' => '',                                        'group' => 'shop'],
            ['key' => 'shop_phone',    'value' => '',                                        'group' => 'shop'],
            ['key' => 'shop_address',  'value' => '',                                        'group' => 'shop'],
            ['key' => 'shop_currency', 'value' => 'EUR',                                     'group' => 'shop'],
            ['key' => 'shop_locale',   'value' => 'fr_FR',                                   'group' => 'shop'],

            // Thème (neutre — modifiable depuis le dashboard)
            ['key' => 'primary_color',        'value' => '#0d6efd', 'group' => 'theme'],
            ['key' => 'primary_text_color',   'value' => '#ffffff', 'group' => 'theme'],
            ['key' => 'secondary_color',      'value' => '#FFB524', 'group' => 'theme'],
            ['key' => 'secondary_text_color', 'value' => '#ffffff', 'group' => 'theme'],

            // Livraison
            ['key' => 'shipping_free_threshold', 'value' => '25000',  'group' => 'shipping'],
            ['key' => 'shipping_cost',           'value' => '2000',   'group' => 'shipping'],
            ['key' => 'shipping_carrier',        'value' => 'Coursier local', 'group' => 'shipping'],

            // Taxes
            ['key' => 'vat_default', 'value' => '20',   'group' => 'tax'],
            ['key' => 'vat_food',    'value' => '5.5',  'group' => 'tax'],

            // Réseaux sociaux
            ['key' => 'facebook',  'value' => '', 'group' => 'social'],
            ['key' => 'instagram', 'value' => '', 'group' => 'social'],
            ['key' => 'tiktok',    'value' => '', 'group' => 'social'],
            ['key' => 'twitter',   'value' => '', 'group' => 'social'],
            ['key' => 'youtube',   'value' => '', 'group' => 'social'],
            ['key' => 'linkedin',  'value' => '', 'group' => 'social'],

            // Notifications WhatsApp
            ['key' => 'whatsapp_phone',   'value' => '',      'group' => 'notifications'],
            ['key' => 'whatsapp_apikey',  'value' => '',      'group' => 'notifications'],
            ['key' => 'whatsapp_enabled', 'value' => 'false', 'group' => 'notifications'],

            // Stock
            ['key' => 'low_stock_threshold', 'value' => '5',   'group' => 'stock'],
            ['key' => 'stock_alert_email',   'value' => '',   'group' => 'stock'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'group' => $setting['group']]
            );
        }
    }
}
