<?php

namespace App\Http\Controllers\Web\Admin;

use App\Events\ShopApproved;
use App\Events\ShopSuspended;
use App\Http\Controllers\Controller;
use App\Models\Shop;

class ShopController extends Controller
{
    public function index()
    {
        $shops = Shop::with(['owner', 'template'])
            ->withCount(['products' => fn ($q) => $q->where('status', 'active')])
            ->latest()
            ->paginate(20);

        return view('dashboard.admin.Shops.index', compact('shops'));
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
