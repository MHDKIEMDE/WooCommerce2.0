<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Review::with(['user:id,name,email', 'product:id,name,slug'])->latest();

        if ($request->filled('status')) {
            $request->status === 'approved'
                ? $query->whereNotNull('approved_at')
                : $query->whereNull('approved_at');
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        return $this->paginated($query->paginate(25));
    }

    public function approve(int $id): JsonResponse
    {
        $review = Review::findOrFail($id);

        if ($review->approved_at) {
            return $this->error('Avis déjà approuvé.', 409);
        }

        $review->update(['approved_at' => now()]);

        $this->recalculateRating($review->product);

        return $this->success(null, 'Avis approuvé.');
    }

    public function destroy(int $id): JsonResponse
    {
        $review  = Review::findOrFail($id);
        $product = $review->product;

        $review->delete();

        $this->recalculateRating($product);

        return $this->success(null, 'Avis supprimé.');
    }

    private function recalculateRating(\App\Models\Product $product): void
    {
        $product->update([
            'rating_avg'   => $product->reviews()->whereNotNull('approved_at')->avg('rating') ?? 0,
            'rating_count' => $product->reviews()->whereNotNull('approved_at')->count(),
        ]);
    }
}
