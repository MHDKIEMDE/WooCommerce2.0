<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SlideController extends Controller
{
    public function index()
    {
        $slides = Slide::orderBy('sort_order')->get();
        return view('dashboard.admin.Slides.index', compact('slides'));
    }

    public function create()
    {
        return view('dashboard.admin.Slides.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'nullable|string|max:255',
            'subtitle'    => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:100',
            'button_url'  => 'nullable|string|max:255',
            'image'       => 'required|image|max:2048',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ]);

        $data['image_path'] = $request->file('image')->store('slides', 'public');
        $data['is_active']  = $request->boolean('is_active', true);
        unset($data['image']);

        Slide::create($data);

        return redirect()->route('admin.slides.index')
            ->with('success', 'Slide ajouté avec succès.');
    }

    public function edit(Slide $slide)
    {
        return view('dashboard.admin.Slides.edit', compact('slide'));
    }

    public function update(Request $request, Slide $slide)
    {
        $data = $request->validate([
            'title'       => 'nullable|string|max:255',
            'subtitle'    => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:100',
            'button_url'  => 'nullable|string|max:255',
            'image'       => 'nullable|image|max:2048',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($slide->image_path);
            $data['image_path'] = $request->file('image')->store('slides', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', false);
        unset($data['image']);

        $slide->update($data);

        return redirect()->route('admin.slides.index')
            ->with('success', 'Slide mis à jour.');
    }

    public function destroy(Slide $slide)
    {
        Storage::disk('public')->delete($slide->image_path);
        $slide->delete();

        return redirect()->route('admin.slides.index')
            ->with('success', 'Slide supprimé.');
    }
}
