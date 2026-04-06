<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name  = fake()->unique()->words(3, true);
        $price = fake()->numberBetween(500, 25000); // FCFA

        return [
            'name'                => ucfirst($name),
            'slug'                => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 9999),
            'short_description'   => fake()->sentence(),
            'description'         => fake()->paragraph(),
            'price'               => $price,
            'compare_price'       => fake()->optional(0.3)->numberBetween($price, (int)($price * 1.5)),
            'cost_price'          => (int)($price * 0.6),
            'sku'                 => strtoupper(fake()->unique()->bothify('SKU-???-###')),
            'stock_quantity'      => fake()->numberBetween(0, 200),
            'low_stock_threshold' => 5,
            'status'              => 'active',
            'featured'            => false,
            'rating_avg'          => fake()->randomFloat(1, 1, 5),
            'rating_count'        => fake()->numberBetween(0, 100),
            'weight'              => fake()->optional()->randomFloat(2, 0.1, 5),
            'vat_rate'            => 18.00, // TVA Côte d'Ivoire
            'category_id'        => Category::factory(),
        ];
    }

    public function draft(): static
    {
        return $this->state(['status' => 'draft']);
    }

    public function archived(): static
    {
        return $this->state(['status' => 'archived']);
    }

    public function featured(): static
    {
        return $this->state(['featured' => true]);
    }

    public function outOfStock(): static
    {
        return $this->state(['stock_quantity' => 0]);
    }
}
