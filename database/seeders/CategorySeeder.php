<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Fruits & Légumes', 'children' => ['Fruits de saison', 'Légumes feuilles', 'Légumineuses']],
            ['name' => 'Produits Laitiers',  'children' => ['Fromages', 'Yaourts', 'Beurre & Crème']],
            ['name' => 'Viandes & Poissons', 'children' => ['Viandes bio', 'Poissons frais', 'Charcuterie']],
            ['name' => 'Épicerie',            'children' => ['Huiles & Vinaigres', 'Céréales', 'Conserves']],
            ['name' => 'Boissons',            'children' => ['Jus de fruits', 'Infusions', 'Eaux']],
        ];

        foreach ($categories as $catData) {
            $parent = Category::create([
                'name'       => $catData['name'],
                'slug'       => Str::slug($catData['name']),
                'is_active'  => true,
                'sort_order' => 0,
            ]);

            foreach ($catData['children'] as $i => $childName) {
                Category::create([
                    'name'       => $childName,
                    'slug'       => Str::slug($childName),
                    'parent_id'  => $parent->id,
                    'is_active'  => true,
                    'sort_order' => $i,
                ]);
            }
        }
    }
}
