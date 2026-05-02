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
                ->with(['category:id,name,slug', 'brand:id,name,slug', 'images', 'shop:id,name,slug,template_id', 'shop.template:id,name,slug,icon'])
                ->where('status', 'active')
                ->whereHas('shop', fn ($q) => $q->where('status', 'active'));

            if ($request->filled('category')) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
            }

            if ($request->filled('brand')) {
                $query->whereHas('brand', fn ($q) => $q->where('slug', $request->brand));
            }

            // Filtre par boutique (slug)
            if ($request->filled('shop')) {
                $query->whereHas('shop', fn ($q) => $q->where('slug', $request->shop));
            }

            // Filtre par niche / template (slug : food, mode, digital…)
            if ($request->filled('niche')) {
                $query->whereHas('shop.template', fn ($q) => $q->where('slug', $request->niche));
            }

            if ($request->boolean('featured')) {
                $query->where('featured', true);
            }

            if ($request->filled('min_price')) {
                $query->where('price', '>=', (float) $request->min_price);
            }

            if ($request->filled('max_price')) {
                $query->where('price', '<=', (float) $request->max_price);
            }

            if ($request->filled('min_rating')) {
                $query->where('rating_avg', '>=', (float) $request->min_rating);
            }

            if ($request->boolean('in_stock')) {
                $query->where('stock_quantity', '>', 0);
            }

            [$sortField, $sortDirection] = match ($request->input('sort')) {
                'price_asc'  => ['price', 'asc'],
                'price_desc' => ['price', 'desc'],
                'name_asc'   => ['name', 'asc'],
                'popular'    => ['rating_count', 'desc'],
                'top_rated'  => ['rating_avg', 'desc'],
                default      => ['created_at', 'desc'],
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
            return Product::with(['category', 'brand', 'images', 'attributes', 'variants', 'shop:id,name,slug,template_id', 'shop.template:id,name,slug,icon'])
                ->where('slug', $slug)
                ->where('status', 'active')
                ->firstOrFail();
        });

        return $this->success(new ProductResource($product));
    }
}
