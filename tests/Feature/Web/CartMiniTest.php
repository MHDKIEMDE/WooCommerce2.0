<?php

namespace Tests\Feature\Web;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartMiniTest extends TestCase
{
    use RefreshDatabase;

    public function test_mini_cart_returns_json(): void
    {
        $this->getJson('/cart/mini')
            ->assertOk()
            ->assertJsonStructure(['count', 'total', 'items']);
    }

    public function test_mini_cart_count_is_zero_for_empty_cart(): void
    {
        $this->getJson('/cart/mini')
            ->assertOk()
            ->assertJson(['count' => 0]);
    }

    public function test_add_to_cart_returns_json_when_ajax(): void
    {
        $product = Product::factory()->create(['status' => 'active', 'stock_quantity' => 10]);

        $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1])
            ->assertOk()
            ->assertJsonStructure(['success', 'message', 'count'])
            ->assertJson(['success' => true]);
    }

    public function test_add_to_cart_ajax_returns_422_when_stock_insufficient(): void
    {
        $product = Product::factory()->create(['status' => 'active', 'stock_quantity' => 0]);

        $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1])
            ->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_add_to_cart_persists_item_in_database_for_auth_user(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create(['status' => 'active', 'stock_quantity' => 10]);

        $this->actingAs($user)
            ->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 2]);

        $this->assertDatabaseHas('cart_items', [
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'quantity'   => 2,
        ]);
    }

    public function test_cart_items_are_scoped_to_user_in_database(): void
    {
        $userA   = User::factory()->create();
        $userB   = User::factory()->create();
        $product = Product::factory()->create(['status' => 'active', 'stock_quantity' => 10]);

        $this->actingAs($userA)
            ->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);

        // L'article appartient bien à userA
        $this->assertDatabaseHas('cart_items', ['user_id' => $userA->id, 'product_id' => $product->id]);

        // userB n'a pas cet article
        $this->assertDatabaseMissing('cart_items', ['user_id' => $userB->id, 'product_id' => $product->id]);
    }
}
