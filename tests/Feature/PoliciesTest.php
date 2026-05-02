<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Review;
use App\Models\Shop;
use App\Models\ShopPalette;
use App\Models\ShopTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PoliciesTest extends TestCase
{
    use RefreshDatabase;

    private function makeShop(User $owner): Shop
    {
        $tpl = ShopTemplate::factory()->create();
        $pal = ShopPalette::factory()->create(['template_id' => $tpl->id]);
        return Shop::factory()->create(['user_id' => $owner->id, 'template_id' => $tpl->id, 'palette_id' => $pal->id]);
    }

    // ── ShopPolicy ────────────────────────────────────────────────────────────

    public function test_owner_can_update_own_shop(): void
    {
        $owner = User::factory()->create(['role' => 'seller']);
        $shop  = $this->makeShop($owner);

        $this->assertTrue($owner->can('update', $shop));
    }

    public function test_other_seller_cannot_update_shop(): void
    {
        $owner = User::factory()->create(['role' => 'seller']);
        $other = User::factory()->create(['role' => 'seller']);
        $shop  = $this->makeShop($owner);

        $this->assertFalse($other->can('update', $shop));
    }

    public function test_admin_can_update_any_shop(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'seller']);
        $shop  = $this->makeShop($owner);

        $this->assertTrue($admin->can('update', $shop));
    }

    // ── ProductPolicy ─────────────────────────────────────────────────────────

    public function test_owner_can_update_own_product(): void
    {
        $owner   = User::factory()->create(['role' => 'seller']);
        $shop    = $this->makeShop($owner);
        $product = Product::factory()->create(['shop_id' => $shop->id]);

        $this->assertTrue($owner->can('update', $product));
    }

    public function test_other_seller_cannot_update_product(): void
    {
        $owner   = User::factory()->create(['role' => 'seller']);
        $other   = User::factory()->create(['role' => 'seller']);
        $shop    = $this->makeShop($owner);
        $product = Product::factory()->create(['shop_id' => $shop->id]);

        $this->assertFalse($other->can('update', $product));
    }

    // ── ReviewPolicy ─────────────────────────────────────────────────────────

    public function test_author_can_delete_own_review(): void
    {
        $user   = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->can('delete', $review));
    }

    public function test_other_user_cannot_delete_review(): void
    {
        $author = User::factory()->create();
        $other  = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $author->id]);

        $this->assertFalse($other->can('delete', $review));
    }

    public function test_admin_can_delete_any_review(): void
    {
        $admin  = User::factory()->create(['role' => 'admin']);
        $review = Review::factory()->create();

        $this->assertTrue($admin->can('delete', $review));
    }
}
