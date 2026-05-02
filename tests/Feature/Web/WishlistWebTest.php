<?php

namespace Tests\Feature\Web;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistWebTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/account/wishlist')
            ->assertRedirect('/login');
    }

    public function test_authenticated_user_sees_empty_wishlist(): void
    {
        $this->actingAs($this->user)
            ->get('/account/wishlist')
            ->assertOk()
            ->assertSee('liste de souhaits est vide');
    }

    public function test_wishlist_shows_added_products(): void
    {
        $product = Product::factory()->create(['status' => 'active']);
        Wishlist::create(['user_id' => $this->user->id, 'product_id' => $product->id]);

        $this->actingAs($this->user)
            ->get('/account/wishlist')
            ->assertOk()
            ->assertSee($product->name);
    }

    public function test_toggle_adds_product_to_wishlist(): void
    {
        $product = Product::factory()->create(['status' => 'active']);

        $this->actingAs($this->user)
            ->post('/account/wishlist/toggle', ['product_id' => $product->id])
            ->assertRedirect();

        $this->assertDatabaseHas('wishlists', [
            'user_id'    => $this->user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_toggle_removes_product_already_in_wishlist(): void
    {
        $product = Product::factory()->create(['status' => 'active']);
        Wishlist::create(['user_id' => $this->user->id, 'product_id' => $product->id]);

        $this->actingAs($this->user)
            ->post('/account/wishlist/toggle', ['product_id' => $product->id])
            ->assertRedirect();

        $this->assertDatabaseMissing('wishlists', [
            'user_id'    => $this->user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_toggle_requires_valid_product_id(): void
    {
        $this->actingAs($this->user)
            ->post('/account/wishlist/toggle', ['product_id' => 99999])
            ->assertSessionHasErrors();
    }

    public function test_wishlist_is_scoped_to_authenticated_user(): void
    {
        $other = User::factory()->create();
        $product = Product::factory()->create(['status' => 'active']);
        Wishlist::create(['user_id' => $other->id, 'product_id' => $product->id]);

        $this->actingAs($this->user)
            ->get('/account/wishlist')
            ->assertOk()
            ->assertDontSee($product->name);
    }
}
