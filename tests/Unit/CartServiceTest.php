<?php

namespace Tests\Unit;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
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

    public function test_shipping_is_free_above_50_euros(): void
    {
        $items = $this->makeCartItems(60.0);

        $totals = $this->cart->calculateTotals($items);

        $this->assertEquals(0.0, $totals['shippingCost']);
    }

    public function test_shipping_is_5_90_below_50_euros(): void
    {
        $items = $this->makeCartItems(30.0);

        $totals = $this->cart->calculateTotals($items);

        $this->assertEquals(5.90, $totals['shippingCost']);
    }

    public function test_tva_20_percent_applied(): void
    {
        $items = $this->makeCartItems(100.0);

        $totals = $this->cart->calculateTotals($items);

        $this->assertEquals(20.0, $totals['taxAmount']);
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

    public function test_total_above_50_no_shipping(): void
    {
        $items = $this->makeCartItems(50.0);   // 50€ → frais port gratuits

        $totals = $this->cart->calculateTotals($items);

        $this->assertEquals(50.0, $totals['total']);
        $this->assertEquals(0.0, $totals['shippingCost']);
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

    // ── Helpers ──────────────────────────────────────────────────────────

    /**
     * Crée de vrais CartItem en base pour pouvoir tester calculateTotals.
     */
    private function makeCartItems(float $price, int $quantity = 1): Collection
    {
        $product = Product::factory()->create([
            'status'         => 'active',
            'price'          => $price,
            'stock_quantity' => 100,
        ]);

        $item = CartItem::create([
            'session_id' => 'test-session',
            'product_id' => $product->id,
            'quantity'   => $quantity,
        ]);

        return CartItem::with(['product', 'variant'])
            ->where('session_id', 'test-session')
            ->get();
    }
}
