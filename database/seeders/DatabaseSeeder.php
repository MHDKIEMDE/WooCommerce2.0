<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ShopTemplateSeeder::class, // 0. Templates & palettes marketplace
            CategorySeeder::class,     // 1. Catégories (arborescence)
            UserSeeder::class,         // 2. Admin + clients démo
            ProductSeeder::class,      // 3. Produits réalistes (dépend des catégories)
            CouponSeeder::class,       // 4. Codes promo
            SettingSeeder::class,      // 5. Configuration boutique
        ]);
    }
}
