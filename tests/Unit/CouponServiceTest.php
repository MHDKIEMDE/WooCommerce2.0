<?php

namespace Tests\Unit;

use App\Models\Coupon;
use App\Services\CouponService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponServiceTest extends TestCase
{
    use RefreshDatabase;

    private CouponService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CouponService();
    }

    public function test_valid_percent_coupon_is_accepted(): void
    {
        $coupon = Coupon::factory()->create([
            'type'      => 'percent',
            'value'     => 20,
            'min_order' => 0,
            'is_active' => true,
            'expires_at' => null,
        ]);

        $result = $this->service->validate($coupon->code, 100.0);

        $this->assertTrue($result['valid']);
    }

    public function test_coupon_rejected_when_order_below_minimum(): void
    {
        $coupon = Coupon::factory()->create([
            'is_active' => true,
            'min_order' => 50.0,
            'expires_at' => null,
        ]);

        $result = $this->service->validate($coupon->code, 30.0);

        $this->assertFalse($result['valid']);
    }

    public function test_coupon_rejected_when_expired(): void
    {
        $coupon = Coupon::factory()->create([
            'is_active'  => true,
            'expires_at' => now()->subDay(),
        ]);

        $result = $this->service->validate($coupon->code, 100.0);

        $this->assertFalse($result['valid']);
    }

    public function test_coupon_rejected_when_usage_limit_reached(): void
    {
        $coupon = Coupon::factory()->create([
            'is_active'   => true,
            'usage_limit' => 10,
            'used_count'  => 10,
            'expires_at'  => null,
        ]);

        $result = $this->service->validate($coupon->code, 100.0);

        $this->assertFalse($result['valid']);
    }

    public function test_percent_discount_calculated_correctly(): void
    {
        $coupon = Coupon::factory()->make(['type' => 'percent', 'value' => 10]);

        $discount = $this->service->calculateDiscount($coupon, 200.0);

        $this->assertEquals(20.0, $discount);
    }

    public function test_fixed_discount_capped_at_subtotal(): void
    {
        $coupon = Coupon::factory()->make(['type' => 'fixed', 'value' => 150]);

        $discount = $this->service->calculateDiscount($coupon, 50.0);

        $this->assertEquals(50.0, $discount);
    }

    public function test_unknown_coupon_code_returns_invalid(): void
    {
        $result = $this->service->validate('INVALID_CODE', 100.0);

        $this->assertFalse($result['valid']);
    }
}
