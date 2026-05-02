<?php

namespace Tests\Unit;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Shop;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    private CartService $cart;
    private StockService $stockService;
    private CouponService $couponService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockService  = Mockery::mock(StockService::class);
        $this->couponService = Mockery::mock(CouponService::class);

        $this->cart = new CartService($this->stockService, $this->couponService);
    }

    // ── calculateTotals ──────────────────────────────────────────────────

    public function test_subtotal_equals_price_times_quantity(): void
    {
        $items = $this->makeCartItems(100.0, 2);

        $totals = $this->cart->calculateTotals($items);

        $this->assertEquals(200.0, $totals['subtotal']);
    }

    public function test_tva_applied_on_subtotal(): void
    {
        $items = $this->makeCartItems(100.0);

        $totals = $this->cart->calculateTotals($items);

        $this->assertGreaterThan(0, $totals['taxAmount']);
    }

    public function test_discount_applied_from_coupon(): void
    {
        $items  = $this->makeCartItems(100.0);
        $coupon = Coupon::factory()->make(['type' => 'percent', 'value' => 10]);

        $this->couponService
            ->shouldReceive('calculateDiscount')
            ->once()
            ->with($coupon, 100.0)
            ->andReturn(10.0);

        $totals = $this->cart->calculateTotals($items, $coupon);

        $this->assertEquals(10.0, $totals['discount']);
    }

    public function test_total_includes_subtotal_minus_discount(): void
    {
        $items  = $this->makeCartItems(100.0);
        $coupon = Coupon::factory()->make(['type' => 'percent', 'value' => 20]);

        $this->couponService
            ->shouldReceive('calculateDiscount')
            ->once()
            ->andReturn(20.0);

        $totals = $this->cart->calculateTotals($items, $coupon);

        $this->assertLessThan(100.0, $totals['total']);
    }

    // ── addItem ──────────────────────────────────────────────────────────

    public function test_add_item_fails_when_stock_insufficient(): void
    {
        $product = Product::factory()->create(['status' => 'active', 'stock_quantity' => 5]);

        $this->stockService
            ->shouldReceive('check')
            ->once()
            ->andReturn(false);

        $result = $this->cart->addItem(null, $product->id, 10);

        $this->assertFalse($result['success']);
    }

    public function test_add_item_succeeds_when_stock_available(): void
    {
        $product = Product::factory()->create(['status' => 'active', 'stock_quantity' => 10]);

        $this->stockService
            ->shouldReceive('check')
            ->once()
            ->andReturn(true);

        $result = $this->cart->addItem(null, $product->id, 2);

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 2]);
    }

    public function test_add_item_fails_for_inactive_product(): void
    {
        $product = Product::factory()->create(['status' => 'archived']);

        $result = $this->cart->addItem(null, $product->id, 1);

        $this->assertFalse($result['success']);
        $this->assertEquals('Produit indisponible.', $result['message']);
    }

    // ── groupByShop ──────────────────────────────────────────────────────

    public function test_group_by_shop_separates_items_per_shop(): void
    {
        $shopA = Shop::factory()->create();
        $shopB = Shop::factory()->create();

        $pA = Product::factory()->create(['status' => 'active', 'shop_id' => $shopA->id]);
        $pB = Product::factory()->create(['status' => 'active', 'shop_id' => $shopB->id]);

        CartItem::create(['session_id' => 'gs-test', 'product_id' => $pA->id, 'quantity' => 1]);
        CartItem::create(['session_id' => 'gs-test', 'product_id' => $pB->id, 'quantity' => 2]);

        $items  = CartItem::with('product')->where('session_id', 'gs-test')->get();
        $groups = $this->cart->groupByShop($items);

        $this->assertCount(2, $groups);
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function makeCartItems(float $price, int $quantity = 1): Collection
    {
        $product = Product::factory()->create([
            'status'         => 'active',
            'price'          => $price,
            'stock_quantity' => 100,
        ]);

        CartItem::create([
            'session_id' => 'test-session',
            'product_id' => $product->id,
            'quantity'   => $quantity,
        ]);

        return CartItem::with(['product', 'variant'])
            ->where('session_id', 'test-session')
            ->get();
    }
}
