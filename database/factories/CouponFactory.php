<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code'        => strtoupper(fake()->unique()->bothify('PROMO-####')),
            'type'        => fake()->randomElement(['percent', 'fixed']),
            'value'       => fake()->randomFloat(2, 5, 30),
            'min_order'   => 0,
            'usage_limit' => null,
            'used_count'  => 0,
            'expires_at'  => fake()->optional(0.3)->dateTimeBetween('now', '+6 months'),
            'is_active'   => true,
        ];
    }

    public function percent(int $value = 10): static
    {
        return $this->state(['type' => 'percent', 'value' => $value]);
    }

    public function fixed(float $value = 5.0): static
    {
        return $this->state(['type' => 'fixed', 'value' => $value]);
    }

    public function expired(): static
    {
        return $this->state(['expires_at' => now()->subDay()]);
    }
}
