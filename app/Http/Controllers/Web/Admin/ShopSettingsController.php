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
            'shop_name'          => 'required|string|max:100',
            'shop_tagline'       => 'nullable|string|max:150',
            'shop_email'         => 'nullable|email|max:150',
            'shop_phone'         => 'nullable|string|max:30',
            'shop_address'       => 'nullable|string|max:255',
            'shop_currency'      => 'nullable|string|max:10',
            'currency_position'  => 'nullable|in:before,after',
            'currency_decimals'  => 'nullable|integer|min:0|max:4',
            'currency_dec_sep'   => 'nullable|string|max:1',
            'currency_thou_sep'  => 'nullable|string|max:1',
        ]);

        Setting::setGroup('shop', $request->only([
            'shop_name', 'shop_tagline', 'shop_email',
            'shop_phone', 'shop_address', 'shop_currency',
            'currency_position', 'currency_decimals',
            'currency_dec_sep', 'currency_thou_sep',
        ]));

        Cache::forget('settings:group:shop');

        return back()->with('success', 'Informations de la boutique mises à jour.');
    }
}
