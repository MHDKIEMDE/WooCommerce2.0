<?php

namespace Database\Seeders;

use App\Models\ShopPalette;
use App\Models\ShopTemplate;
use Illuminate\Database\Seeder;

class ShopTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name'     => 'Vêtements / Mode',
                'slug'     => 'mode',
                'icon'     => '👗',
                'sections' => ['lookbook', 'size_guide', 'new_arrivals', 'promotions', 'reviews', 'faq'],
                'fonts'    => ['Cormorant Garamond', 'Playfair Display', 'Montserrat'],
                'palettes' => [
                    ['name' => 'Noir Couture',  'color_primary' => '#1a1a1a', 'color_accent' => '#c9a84c', 'color_bg' => '#fafaf8', 'color_text' => '#1a1a1a', 'ambiance' => 'Élégance parisienne'],
                    ['name' => 'Rose Nude',     'color_primary' => '#d4a5a5', 'color_accent' => '#8b6f6f', 'color_bg' => '#fff9f9', 'color_text' => '#3a2a2a', 'ambiance' => 'Douceur & féminité'],
                    ['name' => 'Blanc Minimal', 'color_primary' => '#2d2d2d', 'color_accent' => '#e8e0d8', 'color_bg' => '#ffffff', 'color_text' => '#2d2d2d', 'ambiance' => 'Minimalisme chic'],
                    ['name' => 'Bordeaux Luxe', 'color_primary' => '#6d1f2e', 'color_accent' => '#c9a84c', 'color_bg' => '#fdf8f6', 'color_text' => '#2a1a1a', 'ambiance' => 'Luxe & raffinement'],
                    ['name' => 'Denim Blue',    'color_primary' => '#1e3a5f', 'color_accent' => '#e8a838', 'color_bg' => '#f4f7fb', 'color_text' => '#1e2a3a', 'ambiance' => 'Casual & tendance'],
                ],
            ],
            [
                'name'     => 'Alimentaire',
                'slug'     => 'food',
                'icon'     => '🍽️',
                'sections' => ['products', 'recipes', 'producers', 'delivery', 'about', 'reviews'],
                'fonts'    => ['Lora', 'Merriweather', 'Nunito'],
                'palettes' => [
                    ['name' => 'Terre & Épices', 'color_primary' => '#8b4513', 'color_accent' => '#d4a853', 'color_bg' => '#fdf6ec', 'color_text' => '#3a2010', 'ambiance' => 'Chaleur & appétit'],
                    ['name' => 'Vert Marché',    'color_primary' => '#2d6a4f', 'color_accent' => '#f4a261', 'color_bg' => '#f0faf4', 'color_text' => '#1a3a2a', 'ambiance' => 'Fraîcheur & bio'],
                    ['name' => 'Rouge Saveur',   'color_primary' => '#c1440e', 'color_accent' => '#ffd166', 'color_bg' => '#fff8f5', 'color_text' => '#3a1a0a', 'ambiance' => 'Passion & goût'],
                    ['name' => 'Olive Gourmet',  'color_primary' => '#6b705c', 'color_accent' => '#a5a58d', 'color_bg' => '#f8f7f2', 'color_text' => '#2a2a1e', 'ambiance' => 'Terroir & authenticité'],
                    ['name' => 'Bleu Marin',     'color_primary' => '#1d3461', 'color_accent' => '#f4d35e', 'color_bg' => '#f0f4fb', 'color_text' => '#0d1a36', 'ambiance' => 'Mer & fraîcheur'],
                ],
            ],
            [
                'name'     => 'Digital / Formations',
                'slug'     => 'digital',
                'icon'     => '💻',
                'sections' => ['modules', 'testimonials', 'faq', 'certification', 'about', 'pricing'],
                'fonts'    => ['DM Sans', 'Space Grotesk', 'Sora'],
                'palettes' => [
                    ['name' => 'Tech Sombre',    'color_primary' => '#6366f1', 'color_accent' => '#06b6d4', 'color_bg' => '#0f0f23', 'color_text' => '#e8e8f8', 'ambiance' => 'Sérieux & moderne'],
                    ['name' => 'Bleu Confiance', 'color_primary' => '#1d4ed8', 'color_accent' => '#10b981', 'color_bg' => '#f8faff', 'color_text' => '#0f172a', 'ambiance' => 'Clarté & conversion'],
                    ['name' => 'Violet Pro',     'color_primary' => '#7c3aed', 'color_accent' => '#f59e0b', 'color_bg' => '#faf8ff', 'color_text' => '#1e1030', 'ambiance' => 'Créativité & expertise'],
                    ['name' => 'Vert Growth',    'color_primary' => '#059669', 'color_accent' => '#3b82f6', 'color_bg' => '#f0fdf4', 'color_text' => '#022c22', 'ambiance' => 'Croissance & impact'],
                    ['name' => 'Gris Corporate', 'color_primary' => '#374151', 'color_accent' => '#6366f1', 'color_bg' => '#f9fafb', 'color_text' => '#111827', 'ambiance' => 'Sérieux & fiabilité'],
                ],
            ],
            [
                'name'     => 'Artisanat / Fait-main',
                'slug'     => 'craft',
                'icon'     => '🧶',
                'sections' => ['about', 'materials', 'custom_orders', 'workshops', 'reviews', 'faq'],
                'fonts'    => ['Crimson Text', 'Libre Baskerville', 'Raleway'],
                'palettes' => [
                    ['name' => 'Bois & Lin',     'color_primary' => '#8b7355', 'color_accent' => '#a8522a', 'color_bg' => '#fdf8f3', 'color_text' => '#3a2a1a', 'ambiance' => 'Authentique & chaud'],
                    ['name' => 'Sage & Argile',  'color_primary' => '#7d9b76', 'color_accent' => '#c17f5a', 'color_bg' => '#f7f5f0', 'color_text' => '#2a3028', 'ambiance' => 'Nature & sérénité'],
                    ['name' => 'Miel & Brique',  'color_primary' => '#c07d3e', 'color_accent' => '#8b3a2a', 'color_bg' => '#fffbf5', 'color_text' => '#3a2010', 'ambiance' => 'Chaleureux & convivial'],
                    ['name' => 'Crème & Taupe',  'color_primary' => '#9e8e7e', 'color_accent' => '#6b5a4e', 'color_bg' => '#fefcf9', 'color_text' => '#2a2018', 'ambiance' => 'Douceur & élégance'],
                    ['name' => 'Indigo Craft',   'color_primary' => '#3d405b', 'color_accent' => '#e07a5f', 'color_bg' => '#f4f4f8', 'color_text' => '#1a1c2e', 'ambiance' => 'Créatif & original'],
                ],
            ],
            [
                'name'     => 'Tech / Électronique',
                'slug'     => 'tech',
                'icon'     => '⚡',
                'sections' => ['specs', 'comparison', 'warranty', 'support', 'reviews', 'faq'],
                'fonts'    => ['Rajdhani', 'Oxanium', 'Barlow'],
                'palettes' => [
                    ['name' => 'Cyber Noir',     'color_primary' => '#00ff88', 'color_accent' => '#00d4ff', 'color_bg' => '#0a0a0f', 'color_text' => '#e0ffe8', 'ambiance' => 'High-tech futuriste'],
                    ['name' => 'Steel Blue',     'color_primary' => '#0077b6', 'color_accent' => '#00b4d8', 'color_bg' => '#f0f8ff', 'color_text' => '#0d1b2a', 'ambiance' => 'Pro & fiable'],
                    ['name' => 'Dark Matter',    'color_primary' => '#e63946', 'color_accent' => '#457b9d', 'color_bg' => '#1d1d2e', 'color_text' => '#f1faee', 'ambiance' => 'Puissant & audacieux'],
                    ['name' => 'Argent Tech',    'color_primary' => '#4a4e69', 'color_accent' => '#c9ada7', 'color_bg' => '#f2f2f2', 'color_text' => '#1a1a2e', 'ambiance' => 'Sophistiqué & précis'],
                    ['name' => 'Volt Green',     'color_primary' => '#2dc653', 'color_accent' => '#1a7341', 'color_bg' => '#f0fff4', 'color_text' => '#0a2018', 'ambiance' => 'Énergie & performance'],
                ],
            ],
            [
                'name'     => 'Beauté / Cosmétiques',
                'slug'     => 'beauty',
                'icon'     => '✨',
                'sections' => ['rituals', 'ingredients', 'routines', 'reviews', 'about', 'faq'],
                'fonts'    => ['Didact Gothic', 'Josefin Sans', 'Cormorant Garamond'],
                'palettes' => [
                    ['name' => 'Or Rose Luxe',   'color_primary' => '#b5838d', 'color_accent' => '#c9a84c', 'color_bg' => '#fff9f9', 'color_text' => '#3a1a20', 'ambiance' => 'Luxe & sensorialité'],
                    ['name' => 'Nude Naturel',   'color_primary' => '#c9a98a', 'color_accent' => '#8d6748', 'color_bg' => '#fefaf6', 'color_text' => '#3a2a1a', 'ambiance' => 'Naturel & doux'],
                    ['name' => 'Fuchsia Éclat',  'color_primary' => '#c2185b', 'color_accent' => '#f8bbd0', 'color_bg' => '#fff0f5', 'color_text' => '#3a0020', 'ambiance' => 'Énergie & féminité'],
                    ['name' => 'Vert Botanique', 'color_primary' => '#4a7c59', 'color_accent' => '#a8d5a2', 'color_bg' => '#f4fbf5', 'color_text' => '#1a3022', 'ambiance' => 'Bio & bienveillant'],
                    ['name' => 'Améthyste',      'color_primary' => '#7b2d8b', 'color_accent' => '#e8c3f0', 'color_bg' => '#fdf5ff', 'color_text' => '#2a0a38', 'ambiance' => 'Mystère & élégance'],
                ],
            ],
            [
                'name'     => 'Générique',
                'slug'     => 'generic',
                'icon'     => '🏪',
                'sections' => ['about', 'reviews', 'faq', 'delivery', 'contact'],
                'fonts'    => ['Plus Jakarta Sans', 'Outfit', 'Nunito'],
                'palettes' => [
                    ['name' => 'Bleu Pro',       'color_primary' => '#1e40af', 'color_accent' => '#f59e0b', 'color_bg' => '#f8faff', 'color_text' => '#0f172a', 'ambiance' => 'Polyvalent & fiable'],
                    ['name' => 'Vert Succès',    'color_primary' => '#16a34a', 'color_accent' => '#f59e0b', 'color_bg' => '#f0fdf4', 'color_text' => '#052e16', 'ambiance' => 'Dynamique & positif'],
                    ['name' => 'Slate Moderne',  'color_primary' => '#475569', 'color_accent' => '#f97316', 'color_bg' => '#f8fafc', 'color_text' => '#0f172a', 'ambiance' => 'Sobre & efficace'],
                    ['name' => 'Rouge Énergie',  'color_primary' => '#dc2626', 'color_accent' => '#1d4ed8', 'color_bg' => '#fff5f5', 'color_text' => '#1a0505', 'ambiance' => 'Impact & action'],
                    ['name' => 'Violet Créatif', 'color_primary' => '#7c3aed', 'color_accent' => '#10b981', 'color_bg' => '#faf8ff', 'color_text' => '#1e1030', 'ambiance' => 'Original & mémorable'],
                ],
            ],
        ];

        foreach ($templates as $templateData) {
            $palettesData = $templateData['palettes'];
            unset($templateData['palettes']);

            $template = ShopTemplate::updateOrCreate(
                ['slug' => $templateData['slug']],
                $templateData
            );

            foreach ($palettesData as $paletteData) {
                ShopPalette::updateOrCreate(
                    ['template_id' => $template->id, 'name' => $paletteData['name']],
                    $paletteData
                );
            }
        }
    }
}
