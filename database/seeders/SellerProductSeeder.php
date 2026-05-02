<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SellerProductSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            'biofarm-ci' => [
                'category' => 'Fruits & Légumes',
                'products' => [
                    ['name' => 'Banane Plantain Bio', 'price' => 1200, 'cost' => 700, 'stock' => 80, 'desc' => 'Régimes de banane plantain issus de l\'agriculture biologique.'],
                    ['name' => 'Mangue Kent Bio', 'price' => 2500, 'cost' => 1400, 'stock' => 50, 'desc' => 'Mangues Kent sucrées, sans pesticides.'],
                    ['name' => 'Igname Florenté', 'price' => 3000, 'cost' => 1800, 'stock' => 40, 'desc' => 'Igname de qualité supérieure, récoltée à maturité.'],
                    ['name' => 'Tomates Cerises Bio', 'price' => 1800, 'cost' => 900, 'stock' => 60, 'desc' => 'Tomates cerises cultivées sans engrais chimiques.'],
                    ['name' => 'Gombos Frais', 'price' => 900, 'cost' => 400, 'stock' => 100, 'desc' => 'Gombos tendres cueillis du matin.'],
                    ['name' => 'Aubergines Africaines', 'price' => 1100, 'cost' => 600, 'stock' => 70, 'desc' => 'Aubergines violettes locales, fermes et savoureuses.'],
                    ['name' => 'Ananas Pain de Sucre', 'price' => 2000, 'cost' => 1100, 'stock' => 45, 'desc' => 'Ananas ultra-sucrés de Tiassalé, certifiés bio.'],
                ],
            ],
            'poisson-frais-abidjan' => [
                'category' => 'Poissons frais',
                'products' => [
                    ['name' => 'Thiof Entier (kg)', 'price' => 5500, 'cost' => 3200, 'stock' => 30, 'desc' => 'Thiof de ligne pêché dans le golfe de Guinée.'],
                    ['name' => 'Capitaine Fumé', 'price' => 4200, 'cost' => 2500, 'stock' => 40, 'desc' => 'Capitaine entier fumé au bois de mangrove.'],
                    ['name' => 'Crevettes Fraîches (500g)', 'price' => 3800, 'cost' => 2200, 'stock' => 25, 'desc' => 'Crevettes décortiquées livrées sous glace.'],
                    ['name' => 'Sardines Fraîches (kg)', 'price' => 2200, 'cost' => 1200, 'stock' => 60, 'desc' => 'Sardines pêchées chaque matin à Vridi.'],
                    ['name' => 'Attiéké Poisson (plateau)', 'price' => 3500, 'cost' => 2000, 'stock' => 20, 'desc' => 'Attiéké maison accompagné de poisson grillé.'],
                    ['name' => 'Maquereau Grillé', 'price' => 2800, 'cost' => 1500, 'stock' => 35, 'desc' => 'Maquereau mariné aux épices et grillé à la commande.'],
                ],
            ],
            'epicerie-du-quartier' => [
                'category' => 'Épicerie',
                'products' => [
                    ['name' => 'Huile de Palme Rouge (1L)', 'price' => 2500, 'cost' => 1500, 'stock' => 100, 'desc' => 'Huile de palme artisanale non raffinée, riche en bêta-carotène.'],
                    ['name' => 'Piment Habanero Séché', 'price' => 1500, 'cost' => 800, 'stock' => 80, 'desc' => 'Piment séché et moulu, fort et parfumé.'],
                    ['name' => 'Soumbala (Néré fermenté)', 'price' => 1800, 'cost' => 900, 'stock' => 60, 'desc' => 'Condiment traditionnel indispensable pour les sauces ivoiriennes.'],
                    ['name' => 'Huile de Coco Vierge (250ml)', 'price' => 3200, 'cost' => 1800, 'stock' => 50, 'desc' => 'Huile de coco pressée à froid, idéale pour la cuisine et la cosmétique.'],
                    ['name' => 'Poudre de Pistache (100g)', 'price' => 2200, 'cost' => 1200, 'stock' => 40, 'desc' => 'Poudre de pistaches de Côte d\'Ivoire pour les sauces locales.'],
                    ['name' => 'Sel de Mer Iodé (500g)', 'price' => 700, 'cost' => 300, 'stock' => 150, 'desc' => 'Sel de mer artisanal récolté sur le littoral ivoirien.'],
                    ['name' => 'Cube de Bouillon Local x10', 'price' => 1200, 'cost' => 600, 'stock' => 200, 'desc' => 'Bouillon naturel sans exhausteur de goût artificiel.'],
                ],
            ],
            'laiterie-korhogo' => [
                'category' => 'Produits Laitiers',
                'products' => [
                    ['name' => 'Lait Frais Entier (1L)', 'price' => 1500, 'cost' => 900, 'stock' => 60, 'desc' => 'Lait de vache peule collecté le matin même à Korhogo.'],
                    ['name' => 'Fromage Peulh (200g)', 'price' => 2800, 'cost' => 1600, 'stock' => 30, 'desc' => 'Fromage traditionnel à pâte ferme, légèrement salé.'],
                    ['name' => 'Yaourt Nature (pot 500g)', 'price' => 1800, 'cost' => 1000, 'stock' => 45, 'desc' => 'Yaourt fermenté sans additifs, onctueux et légèrement acidulé.'],
                    ['name' => 'Beurre de Vache (250g)', 'price' => 3500, 'cost' => 2000, 'stock' => 25, 'desc' => 'Beurre clarifié (karité animal) du nord ivoirien.'],
                    ['name' => 'Lait Caillé Sucré (500ml)', 'price' => 1200, 'cost' => 700, 'stock' => 50, 'desc' => 'Lait fermenté sucré, rafraîchissant et nutritif.'],
                ],
            ],
            'cereales-bouake' => [
                'category' => 'Céréales',
                'products' => [
                    ['name' => 'Riz Local Étuvé (5kg)', 'price' => 5000, 'cost' => 3200, 'stock' => 80, 'desc' => 'Riz étuvé de la plaine de Bouaké, riche en fibres.'],
                    ['name' => 'Maïs Blanc Séché (2kg)', 'price' => 2200, 'cost' => 1300, 'stock' => 100, 'desc' => 'Maïs blanc séché au soleil, pour attiéké et foutou.'],
                    ['name' => 'Mil Souna (1kg)', 'price' => 1800, 'cost' => 1000, 'stock' => 70, 'desc' => 'Mil à chandelle de variété souna, très nutritif.'],
                    ['name' => 'Farine de Manioc (1kg)', 'price' => 1500, 'cost' => 800, 'stock' => 90, 'desc' => 'Farine blanche fine pour préparer le foufou maison.'],
                    ['name' => 'Sorgho Rouge (1kg)', 'price' => 1600, 'cost' => 900, 'stock' => 60, 'desc' => 'Sorgho rouge du nord, idéal pour le dolo et les bouillies.'],
                    ['name' => 'Fonio Décortiqué (500g)', 'price' => 2500, 'cost' => 1400, 'stock' => 40, 'desc' => 'Fonio précuit sans gluten, à haute valeur nutritive.'],
                    ['name' => 'Haricot Niébé (1kg)', 'price' => 2000, 'cost' => 1100, 'stock' => 75, 'desc' => 'Haricot blanc à œil noir cultivé en savane.'],
                ],
            ],
        ];

        foreach ($catalog as $shopSlug => $data) {
            $shop = Shop::where('slug', $shopSlug)->first();
            if (! $shop) {
                continue;
            }

            $category = Category::where('name', $data['category'])->first();

            foreach ($data['products'] as $p) {
                $slug = Str::slug($p['name']);
                $base = $slug;
                $i    = 1;
                while (Product::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }

                Product::firstOrCreate(
                    ['slug' => $slug],
                    [
                        'shop_id'             => $shop->id,
                        'category_id'         => $category?->id,
                        'name'                => $p['name'],
                        'description'         => $p['desc'],
                        'short_description'   => $p['desc'],
                        'price'               => $p['price'],
                        'compare_price'       => round($p['price'] * 1.15),
                        'cost_price'          => $p['cost'],
                        'sku'                 => strtoupper(Str::random(8)),
                        'stock_quantity'      => $p['stock'],
                        'low_stock_threshold' => 10,
                        'status'              => 'active',
                        'featured'            => false,
                    ]
                );
            }
        }
    }
}
