<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\ShopPalette;
use App\Models\ShopTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        $foodTemplate = ShopTemplate::where('slug', 'food')->first();

        // Map palette name → palette model (within the food template)
        $palettes = $foodTemplate
            ? $foodTemplate->palettes->keyBy('name')
            : collect();

        $vert    = $palettes->get('Vert Marché')   ?? ShopPalette::first();
        $bleu    = $palettes->get('Bleu Marin')    ?? ShopPalette::first();
        $terre   = $palettes->get('Terre & Épices')  ?? ShopPalette::first();
        $olive   = $palettes->get('Olive Gourmet') ?? ShopPalette::first();
        $rouge   = $palettes->get('Rouge Saveur')  ?? ShopPalette::first();

        $shops = [
            [
                'email'       => 'vendeur@example.com',
                'name'        => 'BioFarm CI',
                'slug'        => 'biofarm-ci',
                'subdomain'   => 'biofarm',
                'description' => 'Produits biologiques directs des fermes ivoiriennes.',
                'status'      => 'active',
                'palette'     => $vert,
            ],
            [
                'email'       => 'vendeur2@example.com',
                'name'        => 'Poisson Frais Abidjan',
                'slug'        => 'poisson-frais-abidjan',
                'subdomain'   => 'poissonfrais',
                'description' => 'Poissons et fruits de mer pêchés chaque matin.',
                'status'      => 'active',
                'palette'     => $bleu,
            ],
            [
                'email'       => 'vendeur3@example.com',
                'name'        => 'Épicerie du Quartier',
                'slug'        => 'epicerie-du-quartier',
                'subdomain'   => 'epicerie',
                'description' => 'Épices, condiments et produits locaux.',
                'status'      => 'active',
                'palette'     => $terre,
            ],
            [
                'email'       => 'vendeur4@example.com',
                'name'        => 'Laiterie Korhogo',
                'slug'        => 'laiterie-korhogo',
                'subdomain'   => 'laiterie',
                'description' => 'Lait frais, fromages et yaourts artisanaux du nord.',
                'status'      => 'pending',
                'palette'     => $olive,
            ],
            [
                'email'       => 'vendeur5@example.com',
                'name'        => 'Les Céréales de Bouaké',
                'slug'        => 'cereales-bouake',
                'subdomain'   => 'cereales',
                'description' => 'Riz, mil, maïs et farines locales.',
                'status'      => 'active',
                'palette'     => $rouge,
            ],
        ];

        foreach ($shops as $data) {
            $user = User::where('email', $data['email'])->first();
            if (! $user) {
                continue;
            }

            Shop::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'user_id'         => $user->id,
                    'template_id'     => $foodTemplate?->id,
                    'palette_id'      => $data['palette']?->id,
                    'name'            => $data['name'],
                    'subdomain'       => $data['subdomain'],
                    'description'     => $data['description'],
                    'status'          => $data['status'],
                    'commission_rate' => 5.00,
                ]
            );
        }
    }
}
