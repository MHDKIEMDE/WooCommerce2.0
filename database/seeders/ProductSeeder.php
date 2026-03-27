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
            ['name' => 'Pommes Gala bio (1 kg)',      'price' => 3.90,  'sku' => 'FRT-001', 'category' => 'Fruits de saison',  'featured' => true],
            ['name' => 'Carottes bio (500 g)',         'price' => 1.80,  'sku' => 'LEG-001', 'category' => 'Légumes feuilles',   'featured' => false],
            ['name' => 'Épinards frais (250 g)',       'price' => 2.50,  'sku' => 'LEG-002', 'category' => 'Légumes feuilles',   'featured' => false],
            ['name' => 'Lentilles vertes du Puy (500g)','price'=> 3.20, 'sku' => 'LEG-003', 'category' => 'Légumineuses',       'featured' => false],
            ['name' => 'Fromage de chèvre frais',      'price' => 4.50,  'sku' => 'LAI-001', 'category' => 'Fromages',          'featured' => true],
            ['name' => 'Yaourt nature bio (x6)',       'price' => 3.60,  'sku' => 'LAI-002', 'category' => 'Yaourts',           'featured' => false],
            ['name' => 'Beurre doux fermier (250 g)',  'price' => 3.20,  'sku' => 'LAI-003', 'category' => 'Beurre & Crème',    'featured' => false],
            ['name' => 'Poulet fermier entier (1.5kg)','price' => 14.90, 'sku' => 'VIA-001', 'category' => 'Viandes bio',       'featured' => true],
            ['name' => 'Saumon atlantique (200 g)',    'price' => 8.90,  'sku' => 'POI-001', 'category' => 'Poissons frais',    'featured' => false],
            ['name' => 'Jambon blanc bio (100 g)',     'price' => 2.90,  'sku' => 'CHA-001', 'category' => 'Charcuterie',       'featured' => false],
            ['name' => 'Huile d\'olive extra vierge (50cl)', 'price' => 9.90, 'sku' => 'EPI-001', 'category' => 'Huiles & Vinaigres', 'featured' => true],
            ['name' => 'Quinoa bio (500 g)',           'price' => 4.50,  'sku' => 'EPI-002', 'category' => 'Céréales',          'featured' => false],
            ['name' => 'Haricots rouges bio (400 g)', 'price' => 1.60,  'sku' => 'EPI-003', 'category' => 'Conserves',         'featured' => false],
            ['name' => 'Jus d\'orange pressé (1 L)',  'price' => 4.20,  'sku' => 'BOI-001', 'category' => 'Jus de fruits',     'featured' => false],
            ['name' => 'Tisane bio camomille (20 sachets)', 'price' => 3.80, 'sku' => 'BOI-002', 'category' => 'Infusions', 'featured' => false],
        ];

        foreach ($products as $data) {
            $category = Category::where('name', $data['category'])->first();

            if (! $category) continue;

            Product::create([
                'name'               => $data['name'],
                'slug'               => Str::slug($data['name']) . '-' . strtolower($data['sku']),
                'description'        => "Produit bio de qualité supérieure. Sélectionné avec soin auprès de producteurs locaux engagés dans une agriculture respectueuse de l'environnement.",
                'short_description'  => "Produit frais et naturel, issu de l'agriculture biologique.",
                'price'              => $data['price'],
                'compare_price'      => round($data['price'] * 1.2, 2),
                'cost_price'         => round($data['price'] * 0.6, 2),
                'sku'                => $data['sku'],
                'stock_quantity'     => rand(20, 150),
                'low_stock_threshold'=> 5,
                'category_id'        => $category->id,
                'status'             => 'active',
                'featured'           => $data['featured'],
                'vat_rate'           => 5.50,
                'weight'             => round(rand(100, 2000) / 1000, 2),
            ]);
        }
    }
}
