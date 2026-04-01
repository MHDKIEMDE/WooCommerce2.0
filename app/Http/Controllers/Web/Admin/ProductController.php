<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'images'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('dashboard.admin.Produits.indexProduct', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands     = Brand::orderBy('name')->get();
        return view('dashboard.admin.Produits.createProduct', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price'             => 'required|numeric|min:0',
            'compare_price'     => 'nullable|numeric|min:0',
            'sku'               => 'nullable|string|max:100|unique:products,sku',
            'stock_quantity'    => 'required|integer|min:0',
            'category_id'       => 'required|exists:categories,id',
            'brand_id'          => 'nullable|exists:brands,id',
            'status'            => 'required|in:active,draft,archived',
            'featured'          => 'nullable|boolean',
            'weight'            => 'nullable|numeric|min:0',
            'unit'              => 'nullable|string|max:50',
            'vat_rate'          => 'nullable|numeric|min:0',
            'images.*'          => 'nullable|image|max:2048',
        ]);

        $data['slug']     = Str::slug($data['name']) . '-' . Str::random(5);
        $data['featured'] = $request->boolean('featured');

        $product = Product::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'url'        => \Illuminate\Support\Facades\Storage::url($path),
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        Cache::flush();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produit créé avec succès.');
    }

    public function edit(Product $product)
    {
        $product->load('images', 'category', 'brand');
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands     = Brand::orderBy('name')->get();
        return view('dashboard.admin.Produits.editProduct', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price'             => 'required|numeric|min:0',
            'compare_price'     => 'nullable|numeric|min:0',
            'sku'               => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'stock_quantity'    => 'required|integer|min:0',
            'category_id'       => 'required|exists:categories,id',
            'brand_id'          => 'nullable|exists:brands,id',
            'status'            => 'required|in:active,draft,archived',
            'featured'          => 'nullable|boolean',
            'weight'            => 'nullable|numeric|min:0',
            'unit'              => 'nullable|string|max:50',
            'vat_rate'          => 'nullable|numeric|min:0',
            'images.*'          => 'nullable|image|max:2048',
        ]);

        $data['featured'] = $request->boolean('featured');
        $product->update($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'url'        => \Illuminate\Support\Facades\Storage::url($path),
                    'is_primary' => $product->images->isEmpty() && $index === 0,
                    'sort_order' => $product->images->count() + $index,
                ]);
            }
        }

        Cache::flush();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produit mis à jour.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        Cache::flush();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produit supprimé.');
    }
}
