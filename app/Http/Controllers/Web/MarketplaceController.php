<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopTemplate;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Shop::with(['owner', 'template', 'palette'])
            ->where('status', 'active');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($s) => $s->where('name', 'like', "%$q%")->orWhere('description', 'like', "%$q%"));
        }

        if ($request->filled('niche')) {
            $query->whereHas('template', fn($t) => $t->where('slug', $request->niche));
        }

        $shops     = $query->latest()->paginate(12)->withQueryString();
        $niches    = ShopTemplate::withCount(['shops' => fn($q) => $q->where('status', 'active')])->get();
        $total     = Shop::where('status', 'active')->count();

        return view('marketplace.index', compact('shops', 'niches', 'total'));
    }

    public function show(string $slug): View
    {
        $shop = Shop::with(['owner', 'template', 'palette'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $products = Product::with('images')
            ->where('shop_id', $shop->id)
            ->where('status', 'active')
            ->latest()
            ->paginate(12);

        return view('marketplace.show', compact('shop', 'products'));
    }
}
