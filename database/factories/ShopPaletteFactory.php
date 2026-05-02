<?php

namespace Database\Factories;

use App\Models\ShopTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShopPaletteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'template_id'   => ShopTemplate::factory(),
            'name'          => fake()->unique()->word(),
            'color_primary' => '#3B82F6',
            'color_accent'  => '#10B981',
            'color_bg'      => '#FFFFFF',
            'color_text'    => '#111111',
            'ambiance'      => fake()->randomElement(['light', 'dark', 'nature', 'modern']),
        ];
    }
}
