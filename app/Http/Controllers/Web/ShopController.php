<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShopController extends Controller
{
    public function home()
    {
        $featured = Cache::remember('web:home:featured', now()->addMinutes(10), function () {
            return Product::with(['images', 'category'])
                ->where('status', 'active')
                ->where('featured', true)
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();
        });

        $categories = Cache::remember('categories:all', now()->addMinutes(60), function () {
            return Category::whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->take(6)
                ->get();
        });

        $newArrivals = Cache::remember('web:home:new', now()->addMinutes(10), function () {
            return Product::with(['images', 'category'])
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();
        });

        return view('home', compact('featured', 'categories', 'newArrivals'));
    }

    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['category', 'brand', 'images'])
            ->where('status', 'active');

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('brand')) {
            $query->whereHas('brand', fn ($q) => $q->where('slug', $request->brand));
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

        match ($request->input('sort')) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc'   => $query->orderBy('name', 'asc'),
            'popular'    => $query->orderBy('rating_count', 'desc'),
            'top_rated'  => $query->orderBy('rating_avg', 'desc'),
            default      => $query->orderBy('created_at', 'desc'),
        };

        $products = $query->paginate(16)->withQueryString();

        $categories = Cache::remember('categories:all', now()->addMinutes(60), fn () =>
            Category::whereNull('parent_id')->where('is_active', true)->orderBy('sort_order')->get()
        );

        $brands = Cache::remember('brands:all', now()->addMinutes(60), fn () =>
            Brand::orderBy('name')->get()
        );

        return view('shop', compact('products', 'categories', 'brands'));
    }

    public function show(string $slug)
    {
        $product = Cache::remember("web:product:{$slug}", now()->addMinutes(10), function () use ($slug) {
            return Product::with(['category', 'brand', 'images', 'attributes', 'variants', 'reviews'])
                ->where('slug', $slug)
                ->where('status', 'active')
                ->firstOrFail();
        });

        $related = Cache::remember("web:related:{$slug}", now()->addMinutes(10), function () use ($product) {
            return Product::with(['images'])
                ->where('status', 'active')
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->take(4)
                ->get();
        });

        return view('showProduct', compact('product', 'related'));
    }

    public function category(string $slug, Request $request)
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $products = $category->products()
            ->with(['brand', 'images'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(16)
            ->withQueryString();

        $categories = Cache::remember('categories:all', now()->addMinutes(60), fn () =>
            Category::whereNull('parent_id')->where('is_active', true)->orderBy('sort_order')->get()
        );

        return view('shop', compact('products', 'categories', 'category'));
    }

    public function search(Request $request)
    {
        $q = trim($request->input('q', ''));
        $products = collect();

        if (strlen($q) >= 2) {
            $products = Product::with(['category', 'images'])
                ->where('status', 'active')
                ->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                        ->orWhere('short_description', 'like', "%{$q}%")
                        ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$q}%"));
                })
                ->orderBy('rating_count', 'desc')
                ->paginate(16)
                ->withQueryString();
        }

        return view('shop', compact('products', 'q'));
    }
}
