<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['products' => fn ($q) => $q->where('status', 'active')])
            ->orderBy('sort_order')
            ->get();

        return view('dashboard.admin.Categories.indexCategorie', compact('categories'));
    }

    public function create()
    {
        $parents = Category::whereNull('parent_id')->where('is_active', true)->orderBy('name')->get();
        return view('dashboard.admin.Categories.createCategorie', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|max:1024',
            'parent_id'   => 'nullable|exists:categories,id',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ]);

        $data['slug']      = Str::slug($data['name']) . '-' . Str::random(4);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $data['image_url'] = Storage::url(
                $request->file('image')->store('categories', 'public')
            );
        }

        unset($data['image']);
        Category::create($data);
        Cache::forget('categories:all');
        Cache::forget('home:categories:top5');

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie créée.');
    }

    public function edit(Category $category)
    {
        $parents = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('dashboard.admin.Categories.editCategorie', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|max:1024',
            'parent_id'   => 'nullable|exists:categories,id',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', false);

        if ($request->hasFile('image')) {
            $data['image_url'] = Storage::url(
                $request->file('image')->store('categories', 'public')
            );
        }

        unset($data['image']);
        $category->update($data);
        Cache::forget('categories:all');
        Cache::forget('home:categories:top5');

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie mise à jour.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        Cache::forget('categories:all');
        Cache::forget('home:categories:top5');

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie supprimée.');
    }
}
