<?php

namespace Database\Factories;

use App\Models\ShopPalette;
use App\Models\ShopTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ShopFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'user_id'         => User::factory()->seller(),
            'template_id'     => ShopTemplate::factory(),
            'palette_id'      => ShopPalette::factory(),
            'name'            => $name,
            'slug'            => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 9999),
            'subdomain'       => Str::slug($name) . fake()->unique()->numberBetween(1, 9999),
            'description'     => fake()->sentence(),
            'status'          => 'active',
            'commission_rate' => 5.00,
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }

    public function suspended(): static
    {
        return $this->state(['status' => 'suspended']);
    }
}
