<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ThemeSettingsController extends Controller
{
    public function edit()
    {
        $theme = Setting::getGroup('theme');
        return view('dashboard.admin.Settings.theme', compact('theme'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'primary_color'        => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'primary_text_color'   => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color'      => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_text_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        Setting::setGroup('theme', $request->only([
            'primary_color',
            'primary_text_color',
            'secondary_color',
            'secondary_text_color',
        ]));

        Cache::forget('settings:group:theme');

        return back()->with('success', 'Thème mis à jour. Les changements sont visibles sur le site.');
    }

    /** Convertit #RRGGBB en "R, G, B" pour CSS rgb() */
    public static function hexToRgb(string $hex): string
    {
        $hex = ltrim($hex, '#');
        return implode(', ', [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ]);
    }
}
