<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $categories = Cache::remember('categories:all', now()->addMinutes(60), function () {
            return Category::with('children')
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });

        return $this->success(CategoryResource::collection($categories));
    }

    public function products(string $slug, Request $request): JsonResponse
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $cacheKey = "category:{$slug}:products:" . md5(serialize($request->query()));

        $paginator = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($category, $request) {
            return $category->products()
                ->with(['brand', 'images'])
                ->where('status', 'active')
                ->when($request->boolean('in_stock'), fn ($q) => $q->where('stock_quantity', '>', 0))
                ->when($request->filled('min_price'), fn ($q) => $q->where('price', '>=', $request->min_price))
                ->when($request->filled('max_price'), fn ($q) => $q->where('price', '<=', $request->max_price))
                ->orderBy('created_at', 'desc')
                ->paginate($request->input('per_page', 20));
        });

        return $this->paginated($paginator);
    }
}
