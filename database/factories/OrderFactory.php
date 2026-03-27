<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        $subtotal     = fake()->randomFloat(2, 20, 300);
        $shippingCost = $subtotal >= 50 ? 0 : 5.90;
        $tax          = round($subtotal * 0.20, 2);
        $total        = round($subtotal + $shippingCost, 2);

        $address = [
            'first_name' => fake()->firstName(),
            'last_name'  => fake()->lastName(),
            'address'    => fake()->streetAddress(),
            'city'       => fake()->city(),
            'postcode'   => fake()->postcode(),
            'country'    => 'FR',
        ];

        return [
            'user_id'           => User::factory(),
            'order_number'      => 'CMD-' . date('Y') . '-' . fake()->unique()->numberBetween(10000, 99999),
            'status'            => fake()->randomElement(['pending', 'processing', 'shipped', 'delivered']),
            'subtotal'          => $subtotal,
            'shipping_cost'     => $shippingCost,
            'tax_amount'        => $tax,
            'discount_amount'   => 0,
            'total'             => $total,
            'shipping_address'  => $address,
            'billing_address'   => $address,
            'payment_method'    => 'stripe',
            'payment_status'    => 'paid',
            'payment_reference' => 'pi_' . fake()->unique()->regexify('[A-Za-z0-9]{24}'),
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending', 'payment_status' => 'pending']);
    }

    public function delivered(): static
    {
        return $this->state([
            'status'       => 'delivered',
            'delivered_at' => now(),
        ]);
    }
}
