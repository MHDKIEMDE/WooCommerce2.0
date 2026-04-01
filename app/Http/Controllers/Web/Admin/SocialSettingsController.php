<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SocialSettingsController extends Controller
{
    public function edit()
    {
        $social = Setting::getGroup('social');
        return view('dashboard.admin.Settings.social', compact('social'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'twitter'   => 'nullable|url|max:255',
            'facebook'  => 'nullable|url|max:255',
            'youtube'   => 'nullable|url|max:255',
            'linkedin'  => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'tiktok'    => 'nullable|url|max:255',
        ]);

        Setting::setGroup('social', [
            'twitter'   => $request->input('twitter', ''),
            'facebook'  => $request->input('facebook', ''),
            'youtube'   => $request->input('youtube', ''),
            'linkedin'  => $request->input('linkedin', ''),
            'instagram' => $request->input('instagram', ''),
            'tiktok'    => $request->input('tiktok', ''),
        ]);

        return back()->with('success', 'Liens des réseaux sociaux mis à jour.');
    }
}
