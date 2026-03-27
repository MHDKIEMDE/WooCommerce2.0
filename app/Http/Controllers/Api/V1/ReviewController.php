<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends BaseApiController
{
    /**
     * List approved reviews for a product (public).
     */
    public function index(Request $request, int $product): JsonResponse
    {
        $reviews = Review::where('product_id', $product)
            ->whereNotNull('approved_at')
            ->with('user:id,name')
            ->latest()
            ->paginate(10);

        $data = $reviews->getCollection()->map(fn ($r) => [
            'id'       => $r->id,
            'rating'   => $r->rating,
            'comment'  => $r->comment,
            'author'   => $r->user?->name,
            'date'     => $r->created_at->toIso8601String(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => $data,
            'meta'    => [
                'current_page' => $reviews->currentPage(),
                'last_page'    => $reviews->lastPage(),
                'per_page'     => $reviews->perPage(),
                'total'        => $reviews->total(),
            ],
        ]);
    }

    /**
     * Post a review — requires having purchased the product.
     */
    public function store(Request $request, int $product): JsonResponse
    {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $user = $request->user();

        // Verify the user has purchased this product (delivered order)
        $orderItem = OrderItem::where('product_id', $product)
            ->whereHas('order', fn ($q) =>
                $q->where('user_id', $user->id)
                  ->whereIn('status', ['delivered', 'completed'])
            )
            ->first();

        if (! $orderItem) {
            return $this->error(
                'Vous devez avoir acheté ce produit pour laisser un avis.',
                403
            );
        }

        // One review per product per user
        $alreadyReviewed = Review::where('product_id', $product)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyReviewed) {
            return $this->error('Vous avez déjà laissé un avis pour ce produit.', 409);
        }

        $review = Review::create([
            'product_id' => $product,
            'user_id'    => $user->id,
            'order_id'   => $orderItem->order_id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
        ]);

        return $this->success([
            'id'      => $review->id,
            'rating'  => $review->rating,
            'comment' => $review->comment,
        ], 'Avis envoyé. Il sera visible après modération.', 201);
    }
}
