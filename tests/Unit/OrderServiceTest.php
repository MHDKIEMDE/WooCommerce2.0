<?php

namespace Tests\Unit;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Services\CouponService;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $couponService = Mockery::mock(CouponService::class);
        $this->service = new OrderService($couponService);
    }

    public function test_create_for_shop_creates_order_with_correct_shop(): void
    {
        $seller = User::factory()->seller()->create();
        $buyer  = User::factory()->create();
        $shop   = Shop::factory()->create(['user_id' => $seller->id, 'status' => 'active']);
        $product = Product::factory()->create(['status' => 'active', 'price' => 1000, 'shop_id' => $shop->id]);

        $items = $this->makeItems($product, 2);

        $addr  = ['name' => 'Jean Test', 'address' => '1 rue de la Paix', 'city' => 'Abidjan', 'country' => 'CI', 'phone' => '+225'];
        $order = $this->service->createForShop($buyer, $shop->id, $items, $addr, $addr);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($shop->id, $order->shop_id);
        $this->assertEquals($buyer->id, $order->user_id);
        $this->assertEquals(2000, $order->subtotal);
    }

    public function test_create_for_shop_creates_order_items(): void
    {
        $seller  = User::factory()->seller()->create();
        $buyer   = User::factory()->create();
        $shop    = Shop::factory()->create(['user_id' => $seller->id, 'status' => 'active']);
        $product = Product::factory()->create(['status' => 'active', 'price' => 500, 'shop_id' => $shop->id]);

        $items = $this->makeItems($product, 3);

        $addr  = ['name' => 'Jean Test', 'address' => '1 rue de la Paix', 'city' => 'Abidjan', 'country' => 'CI', 'phone' => '+225'];
        $order = $this->service->createForShop($buyer, $shop->id, $items, $addr, $addr);

        $this->assertCount(1, $order->items);
        $this->assertEquals(3, $order->items->first()->quantity);
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function makeItems(Product $product, int $qty): Collection
    {
        CartItem::create([
            'session_id' => 'order-test',
            'product_id' => $product->id,
            'quantity'   => $qty,
        ]);

        return CartItem::with(['product', 'variant'])->where('session_id', 'order-test')->get();
    }
}
