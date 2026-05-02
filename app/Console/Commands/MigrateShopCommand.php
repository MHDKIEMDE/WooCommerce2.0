<?php

namespace App\Console\Commands;

use App\Models\Shop;
use App\Models\ShopTemplate;
use App\Models\User;
use Illuminate\Console\Command;

class MigrateShopCommand extends Command
{
    protected $signature   = 'marketplace:migrate-shop';
    protected $description = 'Migre shop.monghetto.com en boutique #1 de la marketplace';

    public function handle(): int
    {
        $this->info('Migration de shop.monghetto.com → boutique #1 Monghetto...');

        // 1. Récupérer ou créer le compte vendeur admin
        $admin = User::where('role', 'admin')->first();

        if (! $admin) {
            $this->error('Aucun compte admin trouvé. Lancez d\'abord les seeders.');
            return self::FAILURE;
        }

        // Convertir l'admin en seller pour posséder la boutique migrée
        // On crée plutôt un compte vendeur dédié
        $seller = User::firstOrCreate(
            ['email' => 'shop@monghetto.com'],
            [
                'name'      => 'Monghetto Shop',
                'password'  => bcrypt(\Illuminate\Support\Str::random(32)),
                'role'      => 'seller',
                'is_active' => true,
            ]
        );

        // 2. Template "Alimentaire" pour la boutique Agri-Shop migrée
        $template = ShopTemplate::where('slug', 'food')->first();

        if (! $template) {
            $this->error('Templates non trouvés. Lancez d\'abord: php artisan db:seed --class=ShopTemplateSeeder');
            return self::FAILURE;
        }

        $palette = $template->palettes()->first();

        // 3. Créer la boutique si elle n'existe pas déjà
        $shop = Shop::firstOrCreate(
            ['slug' => 'shop'],
            [
                'user_id'     => $seller->id,
                'template_id' => $template->id,
                'palette_id'  => $palette->id,
                'name'        => 'Monghetto Shop',
                'slug'        => 'shop',
                'subdomain'   => 'shop',
                'description' => 'La boutique originale Monghetto — produits alimentaires de qualité.',
                'status'      => 'active',
            ]
        );

        if ($shop->wasRecentlyCreated) {
            // Créer les sections par défaut
            foreach (($template->sections ?? []) as $i => $sectionType) {
                $shop->sections()->create([
                    'type'       => $sectionType,
                    'is_active'  => true,
                    'sort_order' => $i,
                ]);
            }
            $this->info("✓ Boutique '{$shop->name}' créée (slug: {$shop->slug})");
        } else {
            $this->info("→ Boutique '{$shop->name}' déjà existante, mise à jour ignorée.");
        }

        $this->info('');
        $this->info('Migration terminée.');
        $this->info("  URL boutique : https://shop.monghetto.com");
        $this->info("  Compte vendeur : {$seller->email}");
        $this->info('');
        $this->warn('Prochaine étape : connecter le compte Stripe Express via le dashboard admin.');

        return self::SUCCESS;
    }
}
