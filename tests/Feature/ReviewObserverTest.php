<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewObserverTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduct(): Product
    {
        return Product::factory()->create(['rating_avg' => 0, 'rating_count' => 0]);
    }

    private function approvedReview(Product $product, int $rating): Review
    {
        return Review::factory()->create([
            'product_id'  => $product->id,
            'user_id'     => User::factory()->create()->id,
            'rating'      => $rating,
            'approved_at' => now(),
        ]);
    }

    public function test_rating_avg_updates_on_new_approved_review(): void
    {
        $product = $this->makeProduct();
        $this->approvedReview($product, 4);

        $product->refresh();
        $this->assertEquals(4.00, (float) $product->rating_avg);
        $this->assertEquals(1, $product->rating_count);
    }

    public function test_rating_avg_averages_multiple_reviews(): void
    {
        $product = $this->makeProduct();
        $this->approvedReview($product, 5);
        $this->approvedReview($product, 3);

        $product->refresh();
        $this->assertEquals(4.00, (float) $product->rating_avg);
        $this->assertEquals(2, $product->rating_count);
    }

    public function test_unapproved_review_does_not_count(): void
    {
        $product = $this->makeProduct();
        Review::factory()->create([
            'product_id'  => $product->id,
            'user_id'     => User::factory()->create()->id,
            'rating'      => 1,
            'approved_at' => null,
        ]);

        $product->refresh();
        $this->assertEquals(0.00, (float) $product->rating_avg);
        $this->assertEquals(0, $product->rating_count);
    }

    public function test_rating_recalculates_after_review_deleted(): void
    {
        $product = $this->makeProduct();
        $r1 = $this->approvedReview($product, 5);
        $r2 = $this->approvedReview($product, 3);

        $r2->delete();
        $product->refresh();
        $this->assertEquals(5.00, (float) $product->rating_avg);
        $this->assertEquals(1, $product->rating_count);
    }
}
