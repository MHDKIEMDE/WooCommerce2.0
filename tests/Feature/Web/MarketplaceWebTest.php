<?php

namespace Tests\Feature\Web;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketplace_index_returns_ok(): void
    {
        $this->get('/boutiques')->assertOk();
    }

    public function test_marketplace_index_shows_active_shops_only(): void
    {
        $seller = User::factory()->seller()->create();
        $template = ShopTemplate::factory()->create();

        $active = Shop::factory()->create([
            'user_id'     => $seller->id,
            'template_id' => $template->id,
            'status'      => 'active',
            'name'        => 'BioFarm Active',
        ]);

        $pending = Shop::factory()->create([
            'user_id'     => User::factory()->seller()->create()->id,
            'template_id' => $template->id,
            'status'      => 'pending',
            'name'        => 'Hidden Pending Shop',
        ]);

        $response = $this->get('/boutiques');

        $response->assertOk()
            ->assertSee('BioFarm Active')
            ->assertDontSee('Hidden Pending Shop');
    }

    public function test_marketplace_index_filters_by_search(): void
    {
        $seller = User::factory()->seller()->create();
        $template = ShopTemplate::factory()->create();

        Shop::factory()->create([
            'user_id' => $seller->id, 'template_id' => $template->id,
            'status' => 'active', 'name' => 'Fromagerie du Nord',
        ]);
        Shop::factory()->create([
            'user_id' => User::factory()->seller()->create()->id,
            'template_id' => $template->id,
            'status' => 'active', 'name' => 'Épicerie du Soleil',
        ]);

        $this->get('/boutiques?q=fromag')
            ->assertOk()
            ->assertSee('Fromagerie du Nord')
            ->assertDontSee('Épicerie du Soleil');
    }

    public function test_marketplace_show_returns_ok_for_active_shop(): void
    {
        $shop = Shop::factory()->create([
            'user_id'     => User::factory()->seller()->create()->id,
            'template_id' => ShopTemplate::factory()->create()->id,
            'status'      => 'active',
        ]);

        $this->get("/boutiques/{$shop->slug}")->assertOk();
    }

    public function test_marketplace_show_returns_404_for_pending_shop(): void
    {
        $shop = Shop::factory()->create([
            'user_id'     => User::factory()->seller()->create()->id,
            'template_id' => ShopTemplate::factory()->create()->id,
            'status'      => 'pending',
        ]);

        $this->get("/boutiques/{$shop->slug}")->assertNotFound();
    }

    public function test_marketplace_show_displays_shop_products(): void
    {
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->create([
            'user_id'     => $seller->id,
            'template_id' => ShopTemplate::factory()->create()->id,
            'status'      => 'active',
        ]);

        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'status'  => 'active',
        ]);

        $this->get("/boutiques/{$shop->slug}")
            ->assertOk()
            ->assertSee($product->name);
    }

    public function test_marketplace_show_hides_inactive_products(): void
    {
        $shop = Shop::factory()->create([
            'user_id'     => User::factory()->seller()->create()->id,
            'template_id' => ShopTemplate::factory()->create()->id,
            'status'      => 'active',
        ]);

        $draft = Product::factory()->create([
            'shop_id' => $shop->id,
            'status'  => 'draft',
            'name'    => 'Produit Brouillon Caché',
        ]);

        $this->get("/boutiques/{$shop->slug}")
            ->assertOk()
            ->assertDontSee('Produit Brouillon Caché');
    }
}
