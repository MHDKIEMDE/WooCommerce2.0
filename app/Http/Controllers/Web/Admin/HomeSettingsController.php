<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeSettingsController extends Controller
{
    public function edit()
    {
        $banner = Setting::getGroup('banner');
        $stats  = Setting::getGroup('stats');
        return view('dashboard.admin.HomeSettings.edit', compact('banner', 'stats'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'banner_title'       => 'nullable|string|max:100',
            'banner_subtitle'    => 'nullable|string|max:100',
            'banner_description' => 'nullable|string|max:300',
            'banner_button_text' => 'nullable|string|max:50',
            'banner_button_url'  => 'nullable|string|max:255',
            'banner_image'       => 'nullable|image|max:2048',
            'banner_badge_value' => 'nullable|string|max:20',
            'banner_badge_unit'  => 'nullable|string|max:20',
            'stat_customers'     => 'nullable|string|max:20',
            'stat_quality'       => 'nullable|string|max:20',
            'stat_certificates'  => 'nullable|string|max:20',
            'stat_products'      => 'nullable|string|max:20',
        ]);

        // Banner
        $bannerData = $request->only([
            'banner_title', 'banner_subtitle', 'banner_description',
            'banner_button_text', 'banner_button_url',
            'banner_badge_value', 'banner_badge_unit',
        ]);

        if ($request->hasFile('banner_image')) {
            $old = Setting::get('banner_image');
            if ($old) Storage::disk('public')->delete($old);
            $bannerData['banner_image'] = $request->file('banner_image')->store('banner', 'public');
        }

        foreach ($bannerData as $key => $value) {
            Setting::set($key, $value, 'banner');
        }

        // Stats
        Setting::setGroup('stats', $request->only([
            'stat_customers', 'stat_quality', 'stat_certificates', 'stat_products',
        ]));

        return back()->with('success', 'Paramètres mis à jour.');
    }
}
