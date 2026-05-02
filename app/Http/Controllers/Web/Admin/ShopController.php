<?php

namespace App\Http\Controllers\Web\Admin;

use App\Events\ShopApproved;
use App\Events\ShopSuspended;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Shop;

class ShopController extends Controller
{
    public function index()
    {
        $status = request('status', 'all');

        $query = Shop::with(['owner', 'template'])
            ->withCount('products')
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $shops = $query->paginate(20)->withQueryString();

        return view('dashboard.admin.Shops.index', compact('shops'));
    }

    public function products(Shop $shop)
    {
        $products = Product::with(['category', 'images'])
            ->where('shop_id', $shop->id)
            ->latest()
            ->paginate(20);

        return view('dashboard.admin.Shops.products', compact('shop', 'products'));
    }

    public function toggleProduct(Shop $shop, Product $product)
    {
        abort_unless($product->shop_id === $shop->id, 403);

        $product->update([
            'status' => $product->status === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('success', "Produit « {$product->name} » mis à jour.");
    }

    public function destroyProduct(Shop $shop, Product $product)
    {
        abort_unless($product->shop_id === $shop->id, 403);
        $product->delete();
        return back()->with('success', "Produit supprimé.");
    }

    public function approve(Shop $shop)
    {
        $shop->update(['status' => 'active']);
        event(new ShopApproved($shop));

        return back()->with('success', "Boutique « {$shop->name} » approuvée.");
    }

    public function suspend(Shop $shop)
    {
        $reason = request('reason');
        $shop->update(['status' => 'suspended']);
        event(new ShopSuspended($shop, $reason));

        return back()->with('success', "Boutique « {$shop->name} » suspendue.");
    }
}
