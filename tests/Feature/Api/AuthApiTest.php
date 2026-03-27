<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    // ── Register ─────────────────────────────────────────────────────────

    public function test_register_creates_account_and_returns_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Jean Dupont',
            'email'                 => 'jean@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'device_name'           => 'android-test',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['success', 'data' => ['user', 'token']]);

        $this->assertDatabaseHas('users', ['email' => 'jean@example.com']);
    }

    public function test_register_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'jean@example.com']);

        $response = $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Jean Dupont',
            'email'                 => 'jean@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'device_name'           => 'android-test',
        ]);

        $response->assertStatus(422);
    }

    // ── Login ────────────────────────────────────────────────────────────

    public function test_login_returns_token_on_valid_credentials(): void
    {
        $user = User::factory()->create([
            'password'  => 'password123',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'       => $user->email,
            'password'    => 'password123',
            'device_name' => 'android-test',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['data' => ['token']]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => 'correct_password']);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'       => $user->email,
            'password'    => 'wrong_password',
            'device_name' => 'android-test',
        ]);

        $response->assertStatus(401);
    }

    public function test_login_fails_when_account_inactive(): void
    {
        $user = User::factory()->create([
            'password'  => 'password123',
            'is_active' => false,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'       => $user->email,
            'password'    => 'password123',
            'device_name' => 'android-test',
        ]);

        $response->assertStatus(403);
    }

    // ── Logout ───────────────────────────────────────────────────────────

    public function test_logout_deletes_current_token(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('android-test')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/v1/auth/logout');

        $response->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    // ── Refresh ──────────────────────────────────────────────────────────

    public function test_refresh_issues_new_token(): void
    {
        $user     = User::factory()->create();
        $oldToken = $user->createToken('android-test')->plainTextToken;

        $response = $this->withToken($oldToken)->postJson('/api/v1/auth/refresh');

        $response->assertOk()
            ->assertJsonStructure(['data' => ['token']]);

        $newToken = $response->json('data.token');
        $this->assertNotEquals($oldToken, $newToken);
    }

    // ── OTP Reset ────────────────────────────────────────────────────────

    public function test_forgot_password_stores_otp_in_cache(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertOk();
        $this->assertTrue(Cache::has("otp:reset:{$user->email}"));
    }

    public function test_verify_reset_code_returns_reset_token(): void
    {
        $user = User::factory()->create();
        $otp  = '123456';

        Cache::put("otp:reset:{$user->email}", Hash::make($otp), now()->addMinutes(15));

        $response = $this->postJson('/api/v1/auth/verify-reset-code', [
            'email' => $user->email,
            'otp'   => $otp,
        ]);

        $response->assertOk()
            ->assertJsonStructure(['data' => ['reset_token']]);
    }

    public function test_reset_password_changes_password(): void
    {
        $user       = User::factory()->create();
        $resetToken = 'valid-reset-token';

        Cache::put("reset_token:{$resetToken}", $user->id, now()->addMinutes(10));

        $response = $this->postJson('/api/v1/auth/reset-password', [
            'reset_token'           => $resetToken,
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertOk();
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    // ── Protected routes without token ───────────────────────────────────

    public function test_protected_route_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/account');

        $response->assertStatus(401);
    }
}
