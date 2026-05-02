<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MarketplaceApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_marketplace_home_returns_expected_structure(): void
    {
        $response = $this->getJson('/api/v1/marketplace');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'featured_shops',
                    'featured_products',
                    'new_products',
                    'by_niche',
                    'stats' => ['total_shops', 'total_products', 'total_niches'],
                ],
            ]);
    }

    public function test_marketplace_stats_count_only_active_shops(): void
    {
        Shop::factory()->create(['status' => 'active']);
        Shop::factory()->create(['status' => 'pending']);

        $response = $this->getJson('/api/v1/marketplace');

        $response->assertOk();
        $this->assertEquals(1, $response->json('data.stats.total_shops'));
    }

    public function test_marketplace_shops_returns_paginated_active_shops(): void
    {
        Shop::factory()->count(3)->create(['status' => 'active']);
        Shop::factory()->create(['status' => 'suspended']);

        $response = $this->getJson('/api/v1/marketplace/shops');

        $response->assertOk()
            ->assertJsonStructure(['success', 'data', 'meta']);

        $this->assertEquals(3, $response->json('meta.total'));
    }

    public function test_marketplace_shops_filter_by_niche(): void
    {
        $template = ShopTemplate::factory()->create(['slug' => 'food']);
        $shopInNiche  = Shop::factory()->create(['status' => 'active', 'template_id' => $template->id]);
        $shopOther    = Shop::factory()->create(['status' => 'active']);

        $response = $this->getJson('/api/v1/marketplace/shops?niche=food');

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertTrue($ids->contains($shopInNiche->id));
        $this->assertFalse($ids->contains($shopOther->id));
    }

    public function test_marketplace_niches_returns_templates(): void
    {
        ShopTemplate::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/marketplace/niches');

        $response->assertOk()
            ->assertJsonStructure(['success', 'data']);

        $this->assertGreaterThanOrEqual(3, count($response->json('data')));
    }

    public function test_marketplace_featured_products_only_from_active_shops(): void
    {
        $activeShop    = Shop::factory()->create(['status' => 'active']);
        $suspendedShop = Shop::factory()->create(['status' => 'suspended']);

        Product::factory()->create(['status' => 'active', 'featured' => true, 'shop_id' => $activeShop->id]);
        Product::factory()->create(['status' => 'active', 'featured' => true, 'shop_id' => $suspendedShop->id]);

        $response = $this->getJson('/api/v1/marketplace');

        $response->assertOk();
        $this->assertCount(1, $response->json('data.featured_products'));
    }
}
