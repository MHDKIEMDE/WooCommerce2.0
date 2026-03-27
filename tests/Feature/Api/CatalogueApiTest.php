<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CatalogueApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    // ── Products ─────────────────────────────────────────────────────────

    public function test_products_list_returns_paginated_json(): void
    {
        Product::factory()->count(5)->create(['status' => 'active']);

        $response = $this->getJson('/api/v1/products');

        $response->assertOk()
            ->assertJsonStructure([
                'success', 'data', 'meta' => ['current_page', 'last_page', 'total'],
            ]);
    }

    public function test_products_filtered_by_category(): void
    {
        $cat  = Category::factory()->create();
        $inCat  = Product::factory()->create(['status' => 'active', 'category_id' => $cat->id]);
        $outCat = Product::factory()->create(['status' => 'active']);

        $response = $this->getJson("/api/v1/products?category={$cat->slug}");

        $response->assertOk();

        $ids = collect($response->json('data'))->pluck('id');
        $this->assertTrue($ids->contains($inCat->id));
        $this->assertFalse($ids->contains($outCat->id));
    }

    public function test_product_show_returns_full_detail(): void
    {
        $product = Product::factory()->create(['status' => 'active']);

        $response = $this->getJson("/api/v1/products/{$product->slug}");

        $response->assertOk()
            ->assertJsonStructure(['data' => ['id', 'name', 'slug', 'price']]);
    }

    public function test_inactive_product_returns_404(): void
    {
        $product = Product::factory()->create(['status' => 'archived']);

        $response = $this->getJson("/api/v1/products/{$product->slug}");

        $response->assertNotFound();
    }

    // ── Categories ───────────────────────────────────────────────────────

    public function test_categories_list_returns_root_categories(): void
    {
        Category::factory()->count(3)->create(['parent_id' => null]);

        $response = $this->getJson('/api/v1/categories');

        $response->assertOk()
            ->assertJsonStructure(['success', 'data']);

        $this->assertCount(3, $response->json('data'));
    }

    // ── Search ───────────────────────────────────────────────────────────

    public function test_search_returns_matching_products(): void
    {
        Product::factory()->create(['name' => 'Pommes bio', 'status' => 'active']);
        Product::factory()->create(['name' => 'Carottes', 'status' => 'active']);

        $response = $this->getJson('/api/v1/search?q=pomme');

        $response->assertOk();

        $names = collect($response->json('data'))->pluck('name');
        $this->assertTrue($names->contains('Pommes bio'));
        $this->assertFalse($names->contains('Carottes'));
    }

    public function test_search_requires_query_parameter(): void
    {
        $response = $this->getJson('/api/v1/search');

        $response->assertStatus(422);
    }
}
