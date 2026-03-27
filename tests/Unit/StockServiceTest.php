<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    private StockService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new StockService();
    }

    public function test_check_returns_true_when_stock_sufficient(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 10]);

        $this->assertTrue($this->service->check($product->id, 5));
    }

    public function test_check_returns_false_when_stock_insufficient(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 3]);

        $this->assertFalse($this->service->check($product->id, 5));
    }

    public function test_check_returns_false_when_stock_is_zero(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 0]);

        $this->assertFalse($this->service->check($product->id, 1));
    }

    public function test_decrement_reduces_stock(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 10]);

        $this->service->decrement($product->id, 3);

        $this->assertDatabaseHas('products', [
            'id'             => $product->id,
            'stock_quantity' => 7,
        ]);
    }

    public function test_restore_increases_stock(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 5]);

        $this->service->restore($product->id, 3);

        $this->assertDatabaseHas('products', [
            'id'             => $product->id,
            'stock_quantity' => 8,
        ]);
    }
}
