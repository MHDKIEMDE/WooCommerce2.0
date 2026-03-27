<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'products:' . md5(serialize($request->query()));

        $paginator = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request) {
            $query = Product::query()
                ->with(['category', 'brand', 'images'])
                ->where('status', 'active');

            if ($request->filled('category')) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
            }

            if ($request->filled('brand')) {
                $query->whereHas('brand', fn ($q) => $q->where('slug', $request->brand));
            }

            if ($request->boolean('featured')) {
                $query->where('featured', true);
            }

            if ($request->filled('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->filled('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            if ($request->boolean('in_stock')) {
                $query->where('stock_quantity', '>', 0);
            }

            $sortField     = 'created_at';
            $sortDirection = 'desc';

            match ($request->input('sort')) {
                'price_asc'  => [$sortField, $sortDirection] = ['price', 'asc'],
                'price_desc' => [$sortField, $sortDirection] = ['price', 'desc'],
                'name_asc'   => [$sortField, $sortDirection] = ['name', 'asc'],
                'popular'    => [$sortField, $sortDirection] = ['rating_count', 'desc'],
                'top_rated'  => [$sortField, $sortDirection] = ['rating_avg', 'desc'],
                default      => null,
            };

            return $query->orderBy($sortField, $sortDirection)
                ->paginate($request->input('per_page', 20));
        });

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => ProductResource::collection($paginator),
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $cacheKey = "product:{$slug}";

        $product = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($slug) {
            return Product::with(['category', 'brand', 'images', 'attributes', 'variants'])
                ->where('slug', $slug)
                ->where('status', 'active')
                ->firstOrFail();
        });

        return $this->success(new ProductResource($product));
    }
}
