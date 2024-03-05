<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function showTestimonial($id)
    {
        $testimonials = Testimonial::findOrFail($id);

        return view('welcome', compact('testimonials'));
    }

    public function indexTestimonial()
    {
        $testimonials = Testimonial::all();

        return view('testimonial', compact('testimonials'));
    }

    public function storeTestimonial(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'profession' => 'required|string',
            'user_id' => 'required|exists:user,id',

        ]);

        $testimonial = new Testimonial();
        $testimonial->name = $request->name;
        $testimonial->description = $request->description;
        $testimonial->profession = $request->profession;
        $testimonial->user_id = $request->user_id;
        $testimonial->save();

        return redirect()->route('testimonial.create')->with('success', 'testimonial ajouté avec succès.');
    }
}
