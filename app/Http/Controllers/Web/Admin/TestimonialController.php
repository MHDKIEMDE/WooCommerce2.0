<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::latest()->get();
        return view('dashboard.admin.Testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('dashboard.admin.Testimonials.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'profession'  => 'nullable|string|max:100',
            'description' => 'required|string|max:1000',
            'photo'       => 'nullable|image|max:1024',
            'rating'      => 'required|integer|min:1|max:5',
            'is_active'   => 'nullable|boolean',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('testimonials', 'public');
        }
        $data['is_active'] = $request->boolean('is_active', true);

        Testimonial::create($data);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Témoignage ajouté.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('dashboard.admin.Testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'profession'  => 'nullable|string|max:100',
            'description' => 'required|string|max:1000',
            'photo'       => 'nullable|image|max:1024',
            'rating'      => 'required|integer|min:1|max:5',
            'is_active'   => 'nullable|boolean',
        ]);

        if ($request->hasFile('photo')) {
            if ($testimonial->photo) Storage::disk('public')->delete($testimonial->photo);
            $data['photo'] = $request->file('photo')->store('testimonials', 'public');
        }
        $data['is_active'] = $request->boolean('is_active', false);

        $testimonial->update($data);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Témoignage mis à jour.');
    }

    public function destroy(Testimonial $testimonial)
    {
        if ($testimonial->photo) Storage::disk('public')->delete($testimonial->photo);
        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Témoignage supprimé.');
    }
}
