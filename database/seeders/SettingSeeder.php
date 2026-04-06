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
            ['key' => 'shop_name',     'value' => 'Massaka SAS',                             'group' => 'shop'],
            ['key' => 'shop_tagline',  'value' => 'Produits frais livrés chez vous en Côte d\'Ivoire', 'group' => 'shop'],
            ['key' => 'shop_email',    'value' => 'contact@massaka.ci',                      'group' => 'shop'],
            ['key' => 'shop_phone',    'value' => '+225 07 00 00 00 00',                     'group' => 'shop'],
            ['key' => 'shop_address',  'value' => 'Cocody, Abidjan, Côte d\'Ivoire',         'group' => 'shop'],
            ['key' => 'shop_currency', 'value' => 'XOF',                                     'group' => 'shop'],
            ['key' => 'shop_locale',   'value' => 'fr_CI',                                   'group' => 'shop'],

            // Thème (vert agri)
            ['key' => 'primary_color',        'value' => '#81C408', 'group' => 'theme'],
            ['key' => 'primary_text_color',   'value' => '#ffffff', 'group' => 'theme'],
            ['key' => 'secondary_color',      'value' => '#FFB524', 'group' => 'theme'],
            ['key' => 'secondary_text_color', 'value' => '#ffffff', 'group' => 'theme'],

            // Livraison
            ['key' => 'shipping_free_threshold', 'value' => '25000',  'group' => 'shipping'],
            ['key' => 'shipping_cost',           'value' => '2000',   'group' => 'shipping'],
            ['key' => 'shipping_carrier',        'value' => 'Coursier local', 'group' => 'shipping'],

            // Taxes
            ['key' => 'vat_default', 'value' => '18',   'group' => 'tax'],  // TVA CI = 18%
            ['key' => 'vat_food',    'value' => '0',    'group' => 'tax'],  // Produits alimentaires exonérés

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
            ['key' => 'low_stock_threshold', 'value' => '5',                    'group' => 'stock'],
            ['key' => 'stock_alert_email',   'value' => 'admin@massaka.ci',     'group' => 'stock'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'group' => $setting['group']]
            );
        }
    }
}
