<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::orderBy('sort_order')->get();
        return view('dashboard.admin.Promotions.index', compact('promotions'));
    }

    public function create()
    {
        return view('dashboard.admin.Promotions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => 'required|string|max:100',
            'subtitle'   => 'nullable|string|max:150',
            'link_url'   => 'nullable|string|max:255',
            'image'      => 'required|image|max:2048',
            'bg_color'   => 'required|string|max:50',
            'text_theme' => 'required|in:light,dark',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
        ]);

        $data['image_path'] = $request->file('image')->store('promotions', 'public');
        $data['is_active']  = $request->boolean('is_active', true);
        unset($data['image']);

        Promotion::create($data);

        return redirect()->route('admin.promotions.index')->with('success', 'Promotion ajoutée.');
    }

    public function edit(Promotion $promotion)
    {
        return view('dashboard.admin.Promotions.edit', compact('promotion'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $data = $request->validate([
            'title'      => 'required|string|max:100',
            'subtitle'   => 'nullable|string|max:150',
            'link_url'   => 'nullable|string|max:255',
            'image'      => 'nullable|image|max:2048',
            'bg_color'   => 'required|string|max:50',
            'text_theme' => 'required|in:light,dark',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($promotion->image_path);
            $data['image_path'] = $request->file('image')->store('promotions', 'public');
        }
        $data['is_active'] = $request->boolean('is_active', false);
        unset($data['image']);

        $promotion->update($data);

        return redirect()->route('admin.promotions.index')->with('success', 'Promotion mise à jour.');
    }

    public function destroy(Promotion $promotion)
    {
        Storage::disk('public')->delete($promotion->image_path);
        $promotion->delete();

        return redirect()->route('admin.promotions.index')->with('success', 'Promotion supprimée.');
    }
}
