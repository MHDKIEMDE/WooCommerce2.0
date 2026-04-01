<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShopSettingsController extends Controller
{
    public function edit()
    {
        $shop = Setting::getGroup('shop');
        return view('dashboard.admin.Settings.shop', compact('shop'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'shop_name'     => 'required|string|max:100',
            'shop_tagline'  => 'nullable|string|max:150',
            'shop_email'    => 'nullable|email|max:150',
            'shop_phone'    => 'nullable|string|max:30',
            'shop_address'  => 'nullable|string|max:255',
            'shop_currency' => 'nullable|string|max:10',
        ]);

        Setting::setGroup('shop', $request->only([
            'shop_name', 'shop_tagline', 'shop_email',
            'shop_phone', 'shop_address', 'shop_currency',
        ]));

        Cache::forget('settings:group:shop');

        return back()->with('success', 'Informations de la boutique mises à jour.');
    }
}
