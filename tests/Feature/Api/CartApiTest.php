<?php

namespace Tests\Feature\Api;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartApiTest extends TestCase
{
    use RefreshDatabase;

    // ── Panier invité ─────────────────────────────────────────────────────

    public function test_guest_can_view_empty_cart(): void
    {
        $response = $this->getJson('/api/v1/cart');

        $response->assertOk()
            ->assertJsonStructure(['success', 'data', 'meta' => ['count', 'subtotal', 'total']]);
    }

    public function test_guest_can_add_item_to_cart(): void
    {
        $product = Product::factory()->create([
            'status'         => 'active',
            'stock_quantity' => 10,
            'price'          => 5.00,
        ]);

        $response = $this->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'quantity'   => 2,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 2]);
    }

    public function test_add_item_fails_when_stock_insufficient(): void
    {
        $product = Product::factory()->create([
            'status'         => 'active',
            'stock_quantity' => 1,
        ]);

        $response = $this->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'quantity'   => 10,
        ]);

        $response->assertStatus(422);
    }

    // ── Coupon ────────────────────────────────────────────────────────────

    public function test_valid_coupon_can_be_applied(): void
    {
        $coupon = Coupon::factory()->create([
            'is_active'  => true,
            'min_order'  => 0,
            'expires_at' => null,
        ]);

        $response = $this->postJson('/api/v1/cart/coupon', [
            'code' => $coupon->code,
        ]);

        $response->assertOk();
    }

    public function test_invalid_coupon_returns_error(): void
    {
        $response = $this->postJson('/api/v1/cart/coupon', [
            'code' => 'FAKE_CODE',
        ]);

        $response->assertStatus(422);
    }

    // ── Panier connecté ───────────────────────────────────────────────────

    public function test_authenticated_user_cart_is_scoped_to_user(): void
    {
        $user    = User::factory()->create(['is_active' => true]);
        $product = Product::factory()->create(['status' => 'active', 'stock_quantity' => 5]);
        $token   = $user->createToken('test')->plainTextToken;

        $this->withToken($token)->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'quantity'   => 1,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'user_id'    => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_authenticated_user_can_remove_item(): void
    {
        $user    = User::factory()->create(['is_active' => true]);
        $product = Product::factory()->create(['status' => 'active', 'stock_quantity' => 5]);
        $token   = $user->createToken('test')->plainTextToken;

        $this->withToken($token)->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'quantity'   => 1,
        ]);

        $item = \App\Models\CartItem::where('user_id', $user->id)->first();

        $response = $this->withToken($token)->deleteJson("/api/v1/cart/items/{$item->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }
}
