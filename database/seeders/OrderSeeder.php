<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $buyers   = User::where('role', 'buyer')->get();
        $products = Product::where('status', 'active')->get();

        if ($buyers->isEmpty() || $products->isEmpty()) {
            return;
        }

        $statuses = [
            ['status' => 'delivered', 'payment_status' => 'paid'],
            ['status' => 'delivered', 'payment_status' => 'paid'],
            ['status' => 'shipped',   'payment_status' => 'paid'],
            ['status' => 'processing','payment_status' => 'paid'],
            ['status' => 'pending',   'payment_status' => 'pending'],
        ];

        foreach ($buyers as $buyer) {
            $orderCount = rand(1, 4);

            for ($i = 0; $i < $orderCount; $i++) {
                $statusData = $statuses[array_rand($statuses)];
                $itemCount  = rand(1, 4);
                $items      = $products->random(min($itemCount, $products->count()));

                $subtotal = 0;
                $itemData = [];

                foreach ($items as $product) {
                    $qty      = rand(1, 3);
                    $price    = $product->price;
                    $subtotal += $price * $qty;
                    $itemData[] = compact('product', 'qty', 'price');
                }

                $shippingCost = $subtotal >= 5000 ? 0 : 1500;
                $taxAmount    = round($subtotal * 0.18, 2);
                $total        = $subtotal + $shippingCost;

                $address = [
                    'first_name' => explode(' ', $buyer->name)[0],
                    'last_name'  => explode(' ', $buyer->name)[1] ?? '',
                    'address'    => rand(1, 999) . ', Rue ' . rand(1, 50),
                    'city'       => collect(['Abidjan', 'Bouaké', 'Korhogo', 'Daloa', 'San-Pédro'])->random(),
                    'postcode'   => '',
                    'country'    => 'CI',
                    'phone'      => $buyer->phone ?? '',
                ];

                $order = Order::create([
                    'user_id'           => $buyer->id,
                    'order_number'      => 'CMD-' . date('Y') . '-' . str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT),
                    'status'            => $statusData['status'],
                    'subtotal'          => $subtotal,
                    'shipping_cost'     => $shippingCost,
                    'tax_amount'        => $taxAmount,
                    'discount_amount'   => 0,
                    'total'             => $total,
                    'shipping_address'  => $address,
                    'billing_address'   => $address,
                    'payment_method'    => 'stripe',
                    'payment_status'    => $statusData['payment_status'],
                    'payment_reference' => 'pi_' . bin2hex(random_bytes(12)),
                    'shipped_at'        => $statusData['status'] === 'delivered' ? now()->subDays(rand(2, 30)) : null,
                    'delivered_at'      => $statusData['status'] === 'delivered' ? now()->subDays(rand(1, 15)) : null,
                ]);

                foreach ($itemData as $item) {
                    OrderItem::create([
                        'order_id'         => $order->id,
                        'product_id'       => $item['product']->id,
                        'product_name'     => $item['product']->name,
                        'product_sku'      => $item['product']->sku,
                        'quantity'         => $item['qty'],
                        'unit_price'       => $item['price'],
                        'total_price'      => $item['price'] * $item['qty'],
                        'vat_rate'         => $item['product']->vat_rate ?? 18.00,
                        'product_snapshot' => [
                            'id'    => $item['product']->id,
                            'name'  => $item['product']->name,
                            'sku'   => $item['product']->sku,
                            'price' => $item['price'],
                        ],
                    ]);
                }
            }
        }
    }
}
