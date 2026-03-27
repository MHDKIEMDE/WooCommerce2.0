<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProductController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category:id,name', 'brand:id,name', 'primaryImage'])
            ->withTrashed();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn ($q2) => $q2->where('name', 'like', "%{$q}%")->orWhere('sku', 'like', "%{$q}%"));
        }

        if ($request->filled('status')) {
            $request->status === 'trashed'
                ? $query->onlyTrashed()
                : $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $sort = match ($request->input('sort', 'latest')) {
            'price_asc'  => ['price', 'asc'],
            'price_desc' => ['price', 'desc'],
            'stock_asc'  => ['stock_quantity', 'asc'],
            'oldest'     => ['created_at', 'asc'],
            default      => ['created_at', 'desc'],
        };

        $products = $query->orderBy(...$sort)->paginate(25);

        return $this->paginated($products);
    }

    public function show(int $id): JsonResponse
    {
        $product = Product::with(['category', 'brand', 'images', 'attributes', 'variants'])
            ->withTrashed()
            ->findOrFail($id);

        return $this->success(new ProductResource($product));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'              => 'required|string|max:191',
            'short_description' => 'nullable|string',
            'description'       => 'nullable|string',
            'price'             => 'required|numeric|min:0',
            'compare_price'     => 'nullable|numeric|min:0',
            'cost_price'        => 'nullable|numeric|min:0',
            'sku'               => 'required|string|max:100|unique:products,sku',
            'stock_quantity'    => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'category_id'       => 'required|exists:categories,id',
            'brand_id'          => 'nullable|exists:brands,id',
            'status'            => 'required|in:active,inactive,draft',
            'featured'          => 'boolean',
            'weight'            => 'nullable|numeric|min:0',
            'vat_rate'          => 'nullable|numeric|min:0|max:100',
            'meta_title'        => 'nullable|string|max:191',
            'meta_description'  => 'nullable|string',
        ]);

        $product = Product::create($data);

        Cache::tags(['products'])->flush();

        return $this->success(new ProductResource($product), 'Produit créé.', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $product = Product::withTrashed()->findOrFail($id);

        $data = $request->validate([
            'name'              => 'sometimes|required|string|max:191',
            'short_description' => 'nullable|string',
            'description'       => 'nullable|string',
            'price'             => 'sometimes|required|numeric|min:0',
            'compare_price'     => 'nullable|numeric|min:0',
            'cost_price'        => 'nullable|numeric|min:0',
            'sku'               => "sometimes|required|string|max:100|unique:products,sku,{$id}",
            'stock_quantity'    => 'sometimes|required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'category_id'       => 'sometimes|required|exists:categories,id',
            'brand_id'          => 'nullable|exists:brands,id',
            'status'            => 'sometimes|required|in:active,inactive,draft',
            'featured'          => 'boolean',
            'weight'            => 'nullable|numeric|min:0',
            'vat_rate'          => 'nullable|numeric|min:0|max:100',
            'meta_title'        => 'nullable|string|max:191',
            'meta_description'  => 'nullable|string',
        ]);

        $product->update($data);

        Cache::tags(['products'])->flush();

        return $this->success(new ProductResource($product->fresh()), 'Produit mis à jour.');
    }

    public function destroy(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();

        Cache::tags(['products'])->flush();

        return $this->success(null, 'Produit archivé.');
    }

    public function updateStock(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'stock_quantity'      => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
        ]);

        $product = Product::findOrFail($id);
        $product->update($data);

        return $this->success(
            ['stock_quantity' => $product->stock_quantity],
            'Stock mis à jour.'
        );
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|in:active,inactive,draft',
        ]);

        $product = Product::withTrashed()->findOrFail($id);
        $product->update($data);

        Cache::tags(['products'])->flush();

        return $this->success(null, 'Statut mis à jour.');
    }

    public function export(): JsonResponse
    {
        $products = Product::with(['category:id,name', 'brand:id,name'])
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'price', 'stock_quantity', 'status', 'category_id', 'brand_id', 'created_at']);

        return $this->success($products);
    }
}
