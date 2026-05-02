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
            UserSeeder::class,         // 2. Admin + vendeurs + acheteurs démo
            ProductSeeder::class,      // 3. Produits réalistes (dépend des catégories)
            CouponSeeder::class,       // 4. Codes promo
            SettingSeeder::class,      // 5. Configuration boutique
            ShopSeeder::class,         // 6. Boutiques marketplace (dépend des vendeurs)
            OrderSeeder::class,        // 7. Commandes réalistes (dépend des produits + acheteurs)
            ReviewSeeder::class,       // 8. Avis clients (dépend des commandes livrées)
        ]);
    }
}
