<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $categories = Category::withCount('products')
            ->with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return $this->success(CategoryResource::collection($categories));
    }

    public function show(int $id): JsonResponse
    {
        $category = Category::withCount('products')->with('children')->findOrFail($id);

        return $this->success(new CategoryResource($category));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:191',
            'slug'        => 'nullable|string|max:191|unique:categories,slug',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:categories,id',
            'sort_order'  => 'nullable|integer|min:0',
            'image'       => 'nullable|string',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $category = Category::create($data);

        Cache::forget('categories:all');

        return $this->success(new CategoryResource($category), 'Catégorie créée.', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        $data = $request->validate([
            'name'        => 'sometimes|required|string|max:191',
            'slug'        => "nullable|string|max:191|unique:categories,slug,{$id}",
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:categories,id',
            'sort_order'  => 'nullable|integer|min:0',
            'image'       => 'nullable|string',
        ]);

        if (isset($data['name']) && ! isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category->update($data);

        Cache::forget('categories:all');

        return $this->success(new CategoryResource($category->fresh()), 'Catégorie mise à jour.');
    }

    public function destroy(int $id): JsonResponse
    {
        $category = Category::withCount('products')->findOrFail($id);

        if ($category->products_count > 0) {
            return $this->error("Impossible de supprimer : {$category->products_count} produit(s) associé(s).", 422);
        }

        $category->delete();

        Cache::forget('categories:all');

        return $this->success(null, 'Catégorie supprimée.');
    }
}
