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
            $query->where('status', $request->status);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        return $this->paginated($query->paginate(25));
    }

    public function approve(int $id): JsonResponse
    {
        $review = Review::findOrFail($id);
        $review->update(['status' => 'approved']);

        // Recalculate product rating
        $product = $review->product;
        $product->update([
            'rating_avg'   => $product->reviews()->where('status', 'approved')->avg('rating') ?? 0,
            'rating_count' => $product->reviews()->where('status', 'approved')->count(),
        ]);

        return $this->success(null, 'Avis approuvé.');
    }

    public function destroy(int $id): JsonResponse
    {
        $review  = Review::findOrFail($id);
        $product = $review->product;

        $review->delete();

        $product->update([
            'rating_avg'   => $product->reviews()->where('status', 'approved')->avg('rating') ?? 0,
            'rating_count' => $product->reviews()->where('status', 'approved')->count(),
        ]);

        return $this->success(null, 'Avis supprimé.');
    }
}
