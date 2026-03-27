<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $items = Wishlist::where('user_id', $request->user()->id)
            ->with(['product' => fn ($q) => $q->with('primaryImage', 'category:id,name')])
            ->latest()
            ->paginate(20);

        $data = $items->getCollection()->map(fn ($w) => [
            'id'         => $w->id,
            'product'    => new ProductResource($w->product),
            'added_at'   => $w->created_at->toIso8601String(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => $data,
            'meta'    => [
                'current_page' => $items->currentPage(),
                'last_page'    => $items->lastPage(),
                'per_page'     => $items->perPage(),
                'total'        => $items->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $exists = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $request->product_id)
            ->exists();

        if ($exists) {
            return $this->error('Produit déjà dans la wishlist.', 409);
        }

        Wishlist::create([
            'user_id'    => $request->user()->id,
            'product_id' => $request->product_id,
        ]);

        return $this->success(null, 'Produit ajouté à la wishlist.', 201);
    }

    public function destroy(Request $request, int $product): JsonResponse
    {
        $deleted = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $product)
            ->delete();

        if (! $deleted) {
            return $this->error('Produit non trouvé dans la wishlist.', 404);
        }

        return $this->success(null, 'Produit retiré de la wishlist.');
    }
}
