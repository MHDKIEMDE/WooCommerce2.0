<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Setting;
use App\Models\Slide;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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

        $categories = Cache::remember('home:categories:top5', now()->addMinutes(30), function () {
            return Category::whereNull('parent_id')
                ->where('is_active', true)
                ->whereHas('products', fn ($q) => $q->where('status', 'active'))
                ->withCount(['products' => fn ($q) => $q->where('status', 'active')])
                ->orderByDesc('products_count')
                ->take(5)
                ->get();
        });

        $newArrivals = Cache::remember('web:home:new', now()->addMinutes(10), function () {
            return Product::with(['images', 'category'])
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();
        });

        $slides = Cache::remember('home:slides', now()->addMinutes(30), function () {
            return Slide::active()->get();
        });

        $bestsellers = Cache::remember('web:home:bestsellers', now()->addMinutes(15), function () {
            return Product::with(['images', 'category'])
                ->where('status', 'active')
                ->orderByDesc('views_count')
                ->take(6)
                ->get();
        });

        $testimonials = Cache::remember('home:testimonials', now()->addMinutes(30), function () {
            return Testimonial::active()->take(6)->get();
        });

        $promotions = Cache::remember('home:promotions', now()->addMinutes(30), function () {
            return Promotion::active()->take(3)->get();
        });

        $banner = Setting::getGroup('banner');
        $stats  = Setting::getGroup('stats');

        return view('home', compact(
            'featured', 'categories', 'newArrivals', 'slides',
            'bestsellers', 'testimonials', 'promotions', 'banner', 'stats'
        ));
    }

    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['category', 'brand', 'images'])
            ->where('status', 'active')
            ->where('stock_quantity', '>', 0);  // uniquement les produits disponibles

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

        match ($request->input('sort')) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc'   => $query->orderBy('name', 'asc'),
            'popular'    => $query->orderBy('rating_count', 'desc'),
            'top_rated'  => $query->orderBy('rating_avg', 'desc'),
            default      => $query->orderBy('created_at', 'desc'),
        };

        $products = $query->paginate(16)->withQueryString();

        $categories = Cache::remember('categories:with_products', now()->addMinutes(30), fn () =>
            Category::whereNull('parent_id')
                ->where('is_active', true)
                ->whereHas('products', fn ($q) => $q->where('status', 'active')->where('stock_quantity', '>', 0))
                ->withCount(['products' => fn ($q) => $q->where('status', 'active')->where('stock_quantity', '>', 0)])
                ->orderBy('sort_order')
                ->get()
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

        // Incrémenter les vues une seule fois par session par produit
        $sessionKey = "viewed_product_{$product->id}";
        if (! session()->has($sessionKey)) {
            DB::table('products')->where('id', $product->id)->increment('views_count');
            session()->put($sessionKey, true);
            Cache::forget("web:product:{$slug}");
            Cache::forget('web:home:bestsellers');
        }

        $related = Cache::remember("web:related:{$slug}", now()->addMinutes(10), function () use ($product) {
            $items = Product::with(['images'])
                ->where('status', 'active')
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->take(8)
                ->get();

            // Si pas assez de produits dans la catégorie, compléter avec d'autres produits
            if ($items->count() < 6) {
                $extra = Product::with(['images'])
                    ->where('status', 'active')
                    ->where('id', '!=', $product->id)
                    ->whereNotIn('id', $items->pluck('id'))
                    ->inRandomOrder()
                    ->take(8 - $items->count())
                    ->get();
                $items = $items->concat($extra);
            }

            return $items;
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

        $categories = Cache::remember('categories:with_products', now()->addMinutes(30), fn () =>
            Category::whereNull('parent_id')
                ->where('is_active', true)
                ->whereHas('products', fn ($q) => $q->where('status', 'active'))
                ->withCount(['products' => fn ($q) => $q->where('status', 'active')])
                ->orderBy('sort_order')
                ->get()
        );

        return view('shop', compact('products', 'q', 'categories'));
    }

    /** Auto-suggest AJAX : retourne max 8 résultats en JSON */
    public function suggest(Request $request)
    {
        $q = trim($request->input('q', ''));

        if (strlen($q) < 3) {
            return response()->json([]);
        }

        $results = Product::with(['images'])
            ->where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->where(fn ($w) =>
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('short_description', 'like', "%{$q}%")
            )
            ->orderBy('rating_count', 'desc')
            ->limit(8)
            ->get()
            ->map(fn ($p) => [
                'id'    => $p->id,
                'name'  => $p->name,
                'price' => fmt_price($p->price),
                'url'   => route('shop.show', $p->slug),
                'image' => ($p->images->firstWhere('is_primary', true) ?? $p->images->first())?->url
                           ?? asset('img/fruite-item-1.jpg'),
            ]);

        return response()->json($results);
    }
}
