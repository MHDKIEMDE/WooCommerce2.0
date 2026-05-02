<?php

namespace App\Observers;

use App\Models\Review;

class ReviewObserver
{
    public function saved(Review $review): void
    {
        $this->recalculate($review);
    }

    public function deleted(Review $review): void
    {
        $this->recalculate($review);
    }

    private function recalculate(Review $review): void
    {
        $product = $review->product;
        if (! $product) {
            return;
        }

        $stats = $product->reviews()
            ->whereNotNull('approved_at')
            ->selectRaw('COUNT(*) as cnt, AVG(rating) as avg')
            ->first();

        $product->updateQuietly([
            'rating_avg'   => $stats->avg ? round((float) $stats->avg, 2) : 0,
            'rating_count' => (int) $stats->cnt,
        ]);
    }
}
