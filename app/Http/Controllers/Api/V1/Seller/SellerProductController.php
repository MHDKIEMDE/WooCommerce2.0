<?php

namespace App\Http\Controllers\Api\V1\Seller;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SellerProductController extends BaseApiController
{
    private function sellerShop(Request $request)
    {
        $shop = $request->user()->shop;

        if (! $shop) {
            return null;
        }

        return $shop;
    }

    // GET /api/v1/seller/products
    public function index(Request $request): JsonResponse
    {
        $shop = $this->sellerShop($request);
        if (! $shop) return $this->error('Aucune boutique associée.', 404);

        $query = Product::with(['category:id,name', 'primaryImage'])
            ->where('shop_id', $shop->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) =>
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('sku', 'like', "%{$s}%")
            );
        }

        return $this->paginated($query->latest()->paginate(20));
    }

    // GET /api/v1/seller/products/{id}
    public function show(Request $request, int $id): JsonResponse
    {
        $shop    = $this->sellerShop($request);
        if (! $shop) return $this->error('Aucune boutique associée.', 404);

        $product = Product::with(['category', 'images', 'attributes', 'variants'])
            ->where('shop_id', $shop->id)
            ->findOrFail($id);

        return $this->success(new ProductResource($product));
    }

    // POST /api/v1/seller/products
    public function store(Request $request): JsonResponse
    {
        $shop = $this->sellerShop($request);
        if (! $shop) return $this->error('Aucune boutique associée.', 404);

        if ($shop->status !== 'active') {
            return $this->error('Votre boutique doit être validée avant d\'ajouter des produits.', 403);
        }

        $data = $request->validate([
            'name'               => 'required|string|max:191',
            'description'        => 'required|string',
            'short_description'  => 'nullable|string',
            'price'              => 'required|numeric|min:0',
            'compare_price'      => 'nullable|numeric|min:0',
            'sku'                => 'nullable|string|max:100|unique:products,sku',
            'stock_quantity'     => 'required|integer|min:0',
            'low_stock_threshold'=> 'nullable|integer|min:0',
            'category_id'        => 'required|exists:categories,id',
            'brand_id'           => 'nullable|exists:brands,id',
            'weight'             => 'nullable|numeric|min:0',
            'unit'               => 'nullable|string|max:50',
            'vat_rate'           => 'nullable|numeric|min:0|max:100',
            'images'             => 'nullable|array|max:8',
            'images.*'           => 'image|max:2048',
        ]);

        $data['shop_id'] = $shop->id;
        $data['status']  = 'draft';
        $data['slug']    = $this->uniqueSlug($data['name']);

        $images = $request->file('images', []);
        unset($data['images']);

        $product = Product::create($data);

        foreach ($images as $i => $image) {
            $path = $image->store("products/{$product->id}", 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'path'       => $path,
                'is_primary' => $i === 0,
                'sort_order' => $i,
            ]);
        }

        Cache::tags(['products'])->flush();

        return $this->success(
            new ProductResource($product->load(['category', 'images'])),
            'Produit créé.',
            201
        );
    }

    // PATCH /api/v1/seller/products/{id}
    public function update(Request $request, int $id): JsonResponse
    {
        $shop    = $this->sellerShop($request);
        if (! $shop) return $this->error('Aucune boutique associée.', 404);

        $product = Product::where('shop_id', $shop->id)->findOrFail($id);

        $data = $request->validate([
            'name'               => 'sometimes|required|string|max:191',
            'description'        => 'sometimes|required|string',
            'short_description'  => 'nullable|string',
            'price'              => 'sometimes|required|numeric|min:0',
            'compare_price'      => 'nullable|numeric|min:0',
            'sku'                => "sometimes|nullable|string|max:100|unique:products,sku,{$id}",
            'stock_quantity'     => 'sometimes|required|integer|min:0',
            'low_stock_threshold'=> 'nullable|integer|min:0',
            'category_id'        => 'sometimes|required|exists:categories,id',
            'brand_id'           => 'nullable|exists:brands,id',
            'status'             => 'sometimes|in:active,draft,archived',
            'weight'             => 'nullable|numeric|min:0',
            'unit'               => 'nullable|string|max:50',
            'vat_rate'           => 'nullable|numeric|min:0|max:100',
        ]);

        $product->update($data);

        Cache::tags(['products'])->flush();

        return $this->success(new ProductResource($product->fresh()), 'Produit mis à jour.');
    }

    // DELETE /api/v1/seller/products/{id}
    public function destroy(Request $request, int $id): JsonResponse
    {
        $shop    = $this->sellerShop($request);
        if (! $shop) return $this->error('Aucune boutique associée.', 404);

        $product = Product::where('shop_id', $shop->id)->findOrFail($id);
        $product->delete();

        Cache::tags(['products'])->flush();

        return $this->success(null, 'Produit supprimé.');
    }

    // POST /api/v1/seller/products/{id}/images
    public function addImages(Request $request, int $id): JsonResponse
    {
        $shop    = $this->sellerShop($request);
        if (! $shop) return $this->error('Aucune boutique associée.', 404);

        $product = Product::where('shop_id', $shop->id)->findOrFail($id);

        $request->validate([
            'images'   => 'required|array|max:8',
            'images.*' => 'image|max:2048',
        ]);

        $currentCount = $product->images()->count();

        foreach ($request->file('images') as $i => $image) {
            $path = $image->store("products/{$product->id}", 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'path'       => $path,
                'is_primary' => $currentCount === 0 && $i === 0,
                'sort_order' => $currentCount + $i,
            ]);
        }

        return $this->success(
            $product->images()->get(),
            'Images ajoutées.'
        );
    }

    // DELETE /api/v1/seller/products/{id}/images/{imageId}
    public function deleteImage(Request $request, int $id, int $imageId): JsonResponse
    {
        $shop    = $this->sellerShop($request);
        if (! $shop) return $this->error('Aucune boutique associée.', 404);

        $product = Product::where('shop_id', $shop->id)->findOrFail($id);
        $image   = ProductImage::where('product_id', $product->id)->findOrFail($imageId);

        Storage::disk('public')->delete($image->path);
        $image->delete();

        // Réattribuer l'image principale si supprimée
        if ($image->is_primary) {
            $product->images()->oldest('sort_order')->first()?->update(['is_primary' => true]);
        }

        return $this->success(null, 'Image supprimée.');
    }

    // GET /api/v1/shops/{slug}/products (catalogue public d'une boutique)
    public function publicIndex(Request $request, string $slug): JsonResponse
    {
        $shop = \App\Models\Shop::where('slug', $slug)->where('status', 'active')->firstOrFail();

        $query = Product::with(['category:id,name', 'primaryImage'])
            ->where('shop_id', $shop->id)
            ->where('status', 'active');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) =>
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('short_description', 'like', "%{$s}%")
            );
        }

        $sort = match ($request->input('sort', 'latest')) {
            'price_asc'  => ['price', 'asc'],
            'price_desc' => ['price', 'desc'],
            'popular'    => ['rating_avg', 'desc'],
            default      => ['created_at', 'desc'],
        };

        $products = $query->orderBy(...$sort)->paginate(20);

        return $this->paginated($products);
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
