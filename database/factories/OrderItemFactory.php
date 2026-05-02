<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        $product  = Product::inRandomOrder()->first() ?? Product::factory()->create();
        $quantity = fake()->numberBetween(1, 5);
        $price    = $product->price;
        $total    = $price * $quantity;

        return [
            'order_id'         => Order::factory(),
            'product_id'       => $product->id,
            'variant_id'       => null,
            'product_name'     => $product->name,
            'product_sku'      => $product->sku,
            'quantity'         => $quantity,
            'unit_price'       => $price,
            'total_price'      => $total,
            'vat_rate'         => $product->vat_rate ?? 18.00,
            'product_snapshot' => [
                'id'    => $product->id,
                'name'  => $product->name,
                'sku'   => $product->sku,
                'price' => $price,
                'image' => null,
            ],
        ];
    }
}
