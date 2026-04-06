<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Fruits de saison
            ['name' => 'Ananas Victoria (la pièce)',        'price' => 1500,  'compare' => 2000,  'sku' => 'FRT-001', 'category' => 'Fruits de saison',  'featured' => true,  'unit' => 'pièce'],
            ['name' => 'Mangues Kent (1 kg)',                'price' => 2000,  'compare' => 2500,  'sku' => 'FRT-002', 'category' => 'Fruits de saison',  'featured' => true,  'unit' => 'kg'],
            ['name' => 'Bananes plantains (régime 2 kg)',    'price' => 2500,  'compare' => null,  'sku' => 'FRT-003', 'category' => 'Fruits de saison',  'featured' => false, 'unit' => 'régime'],
            ['name' => 'Papayes fraîches (la pièce)',        'price' => 1200,  'compare' => 1500,  'sku' => 'FRT-004', 'category' => 'Fruits de saison',  'featured' => false, 'unit' => 'pièce'],

            // Légumes feuilles
            ['name' => 'Feuilles de manioc (botte)',        'price' => 500,   'compare' => null,  'sku' => 'LEG-001', 'category' => 'Légumes feuilles',   'featured' => false, 'unit' => 'botte'],
            ['name' => 'Épinards frais (250 g)',             'price' => 800,   'compare' => null,  'sku' => 'LEG-002', 'category' => 'Légumes feuilles',   'featured' => false, 'unit' => '250 g'],
            ['name' => 'Gombo frais (500 g)',                'price' => 1000,  'compare' => 1200,  'sku' => 'LEG-003', 'category' => 'Légumes feuilles',   'featured' => false, 'unit' => '500 g'],

            // Légumineuses
            ['name' => 'Haricots blancs secs (1 kg)',        'price' => 1800,  'compare' => 2200,  'sku' => 'LEG-010', 'category' => 'Légumineuses',       'featured' => false, 'unit' => 'kg'],
            ['name' => 'Lentilles corail (500 g)',           'price' => 2000,  'compare' => null,  'sku' => 'LEG-011', 'category' => 'Légumineuses',       'featured' => false, 'unit' => '500 g'],

            // Viandes bio
            ['name' => 'Poulet fermier entier (1,5 kg)',    'price' => 8500,  'compare' => 10000, 'sku' => 'VIA-001', 'category' => 'Viandes bio',       'featured' => true,  'unit' => 'pièce'],
            ['name' => 'Côtelettes d\'agneau (500 g)',      'price' => 7500,  'compare' => null,  'sku' => 'VIA-002', 'category' => 'Viandes bio',       'featured' => false, 'unit' => '500 g'],

            // Poissons frais
            ['name' => 'Tilapia frais (1 kg)',               'price' => 4500,  'compare' => 5000,  'sku' => 'POI-001', 'category' => 'Poissons frais',    'featured' => true,  'unit' => 'kg'],
            ['name' => 'Barracuda fumé (400 g)',             'price' => 5000,  'compare' => null,  'sku' => 'POI-002', 'category' => 'Poissons frais',    'featured' => false, 'unit' => '400 g'],
            ['name' => 'Crevettes décortiquées (500 g)',    'price' => 9000,  'compare' => 10500, 'sku' => 'POI-003', 'category' => 'Poissons frais',    'featured' => false, 'unit' => '500 g'],

            // Épicerie — Huiles
            ['name' => 'Huile de palme rouge (1 L)',        'price' => 2500,  'compare' => 3000,  'sku' => 'EPI-001', 'category' => 'Huiles & Vinaigres', 'featured' => true,  'unit' => 'L'],
            ['name' => 'Huile de coco vierge (500 ml)',    'price' => 5500,  'compare' => null,  'sku' => 'EPI-002', 'category' => 'Huiles & Vinaigres', 'featured' => false, 'unit' => '500 ml'],

            // Céréales
            ['name' => 'Riz local parfumé (5 kg)',          'price' => 6000,  'compare' => 7500,  'sku' => 'CER-001', 'category' => 'Céréales',          'featured' => true,  'unit' => '5 kg'],
            ['name' => 'Attiéké prêt-à-cuire (1 kg)',       'price' => 1500,  'compare' => null,  'sku' => 'CER-002', 'category' => 'Céréales',          'featured' => false, 'unit' => 'kg'],
            ['name' => 'Farine de maïs (1 kg)',              'price' => 1200,  'compare' => null,  'sku' => 'CER-003', 'category' => 'Céréales',          'featured' => false, 'unit' => 'kg'],

            // Boissons
            ['name' => 'Jus de gingembre maison (1 L)',     'price' => 2500,  'compare' => 3000,  'sku' => 'BOI-001', 'category' => 'Jus de fruits',     'featured' => true,  'unit' => 'L'],
            ['name' => 'Bissap (jus de fleurs d\'hibiscus 1 L)', 'price' => 2000, 'compare' => null, 'sku' => 'BOI-002', 'category' => 'Jus de fruits', 'featured' => false, 'unit' => 'L'],
            ['name' => 'Eau minérale plate (1,5 L x6)',     'price' => 3000,  'compare' => null,  'sku' => 'EAU-001', 'category' => 'Eaux',              'featured' => false, 'unit' => 'pack'],
        ];

        foreach ($products as $data) {
            $category = Category::where('name', $data['category'])->first();

            if (! $category) continue;

            $slug = Str::slug($data['name']);
            // Garantir l'unicité du slug
            $suffix = strtolower(str_replace(['SKU-', '-'], ['', ''], $data['sku']));

            Product::firstOrCreate(
                ['sku' => $data['sku']],
                [
                    'name'               => $data['name'],
                    'slug'               => $slug . '-' . $suffix,
                    'description'        => "Produit frais de qualité supérieure, sélectionné avec soin auprès de producteurs locaux engagés pour des pratiques agricoles responsables. Livré directement de nos partenaires à votre domicile.",
                    'short_description'  => "Produit frais et naturel, issu de producteurs locaux de confiance.",
                    'price'              => $data['price'],
                    'compare_price'      => $data['compare'],
                    'cost_price'         => round($data['price'] * 0.6, 0),
                    'stock_quantity'     => rand(20, 150),
                    'low_stock_threshold'=> 5,
                    'category_id'        => $category->id,
                    'status'             => 'active',
                    'featured'           => $data['featured'],
                    'unit'               => $data['unit'] ?? null,
                    'vat_rate'           => 18.00,
                    'weight'             => round(rand(100, 2000) / 1000, 2),
                ]
            );
        }
    }
}
