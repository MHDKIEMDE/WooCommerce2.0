<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id'  => Product::factory(),
            'user_id'     => User::factory(),
            'order_id'    => null,
            'rating'      => fake()->numberBetween(1, 5),
            'comment'     => fake()->optional(0.8)->paragraph(),
            'approved_at' => fake()->optional(0.7)->dateTimeBetween('-6 months', 'now'),
        ];
    }

    public function approved(): static
    {
        return $this->state(['approved_at' => now()]);
    }

    public function pending(): static
    {
        return $this->state(['approved_at' => null]);
    }
}
