<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminApiTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    private function customerUser(): User
    {
        return User::factory()->create(['role' => 'buyer', 'is_active' => true]);
    }

    // ── Accès refusé aux clients ─────────────────────────────────────────

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = $this->customerUser();
        $token    = $customer->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/v1/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_unauthenticated_cannot_access_admin_routes(): void
    {
        $response = $this->getJson('/api/v1/admin/dashboard');

        $response->assertStatus(401);
    }

    // ── Dashboard admin ──────────────────────────────────────────────────

    public function test_admin_dashboard_returns_stats(): void
    {
        $admin = $this->adminUser();
        $token = $admin->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/v1/admin/dashboard');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'stats' => ['revenue', 'orders', 'customers', 'products'],
                    'recent_orders',
                    'top_products',
                ],
            ]);
    }

    // ── Produits admin ───────────────────────────────────────────────────

    public function test_admin_can_list_products(): void
    {
        $admin = $this->adminUser();
        $token = $admin->createToken('test')->plainTextToken;
        Product::factory()->count(3)->create();

        $response = $this->withToken($token)->getJson('/api/v1/admin/products');

        $response->assertOk()
            ->assertJsonStructure(['data', 'meta']);
    }

    public function test_admin_can_create_product(): void
    {
        $admin    = $this->adminUser();
        $token    = $admin->createToken('test')->plainTextToken;
        $category = \App\Models\Category::factory()->create();

        $response = $this->withToken($token)->postJson('/api/v1/admin/products', [
            'name'           => 'Nouveau produit',
            'description'    => 'Description du produit.',
            'sku'            => 'SKU-001',
            'price'          => 9.99,
            'stock_quantity' => 100,
            'category_id'    => $category->id,
            'status'         => 'active',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', ['sku' => 'SKU-001']);
    }

    public function test_admin_can_update_order_status(): void
    {
        $admin = $this->adminUser();
        $token = $admin->createToken('test')->plainTextToken;
        $user  = $this->customerUser();
        $order = \App\Models\Order::factory()->create(['user_id' => $user->id, 'status' => 'pending']);

        $response = $this->withToken($token)->patchJson("/api/v1/admin/orders/{$order->id}/status", [
            'status' => 'processing',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'processing']);
    }

    public function test_admin_can_toggle_user_active_status(): void
    {
        $admin    = $this->adminUser();
        $token    = $admin->createToken('test')->plainTextToken;
        $customer = $this->customerUser();

        $response = $this->withToken($token)->patchJson("/api/v1/admin/users/{$customer->id}/toggle-active");

        $response->assertOk();
        $this->assertDatabaseHas('users', ['id' => $customer->id, 'is_active' => false]);
    }

    public function test_admin_cannot_disable_another_admin(): void
    {
        $admin  = $this->adminUser();
        $admin2 = $this->adminUser();
        $token  = $admin->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->patchJson("/api/v1/admin/users/{$admin2->id}/toggle-active");

        $response->assertStatus(403);
    }
}
