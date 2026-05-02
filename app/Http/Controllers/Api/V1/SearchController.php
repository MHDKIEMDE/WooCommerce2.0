<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));

        if (strlen($q) < 2) {
            return $this->error('Le terme de recherche doit contenir au moins 2 caractères.', 422);
        }

        $query = Product::query()
            ->with(['category:id,name,slug', 'brand:id,name,slug', 'images', 'shop:id,name,slug,template_id', 'shop.template:id,name,slug'])
            ->where('status', 'active')
            ->whereHas('shop', fn ($s) => $s->where('status', 'active'))
            ->where(function ($q2) use ($q) {
                $q2->where('name', 'like', "%{$q}%")
                   ->orWhere('short_description', 'like', "%{$q}%")
                   ->orWhere('sku', 'like', "%{$q}%")
                   ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$q}%"))
                   ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', "%{$q}%"))
                   ->orWhereHas('shop', fn ($s) => $s->where('name', 'like', "%{$q}%"));
            });

        // Filtres optionnels
        if ($request->filled('niche')) {
            $query->whereHas('shop.template', fn ($t) => $t->where('slug', $request->niche));
        }

        if ($request->filled('shop')) {
            $query->whereHas('shop', fn ($s) => $s->where('slug', $request->shop));
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        $paginator = $query
            ->orderByRaw("CASE WHEN name LIKE ? THEN 0 ELSE 1 END", ["{$q}%"])
            ->orderBy('rating_count', 'desc')
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => ProductResource::collection($paginator),
            'meta'    => [
                'query'        => $q,
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ]);
    }
}
