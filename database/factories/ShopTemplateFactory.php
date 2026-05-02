<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ShopTemplateFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'name'     => ucfirst($name),
            'slug'     => Str::slug($name),
            'icon'     => '🏪',
            'fonts'    => ['Inter', 'Roboto'],
            'sections' => ['hero', 'products', 'about'],
        ];
    }
}
