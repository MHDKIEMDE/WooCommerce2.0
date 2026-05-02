<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopPalette;
use App\Models\ShopTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SellerOnboardingController extends Controller
{
    public function show(): View
    {
        $templates = ShopTemplate::with('palettes')->get();

        return view('seller.register', compact('templates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'shop_name'   => 'required|string|max:100|unique:shops,name',
            'description' => 'required|string|max:500',
            'template_id' => 'required|exists:shop_templates,id',
            'palette_id'  => 'required|exists:shop_palettes,id',
        ]);

        $user = $request->user();

        // Promouvoir en vendeur si nécessaire
        if ($user->role === 'buyer') {
            $user->update(['role' => 'seller']);
        }

        // Vérifier qu'il n'a pas déjà une boutique
        if (Shop::where('user_id', $user->id)->exists()) {
            return back()->withErrors(['shop_name' => 'Vous avez déjà une boutique.']);
        }

        $slug      = Str::slug($data['shop_name']);
        $subdomain = $slug;

        // Garantir l'unicité du slug et sous-domaine
        $base = $slug;
        $i    = 1;
        while (Shop::where('slug', $slug)->orWhere('subdomain', $subdomain)->exists()) {
            $slug      = $base . '-' . $i;
            $subdomain = $base . $i;
            $i++;
        }

        Shop::create([
            'user_id'         => $user->id,
            'template_id'     => $data['template_id'],
            'palette_id'      => $data['palette_id'],
            'name'            => $data['shop_name'],
            'slug'            => $slug,
            'subdomain'       => $subdomain,
            'description'     => $data['description'],
            'status'          => 'pending',
            'commission_rate' => 5.00,
        ]);

        return redirect()->route('seller.pending')
            ->with('success', 'Votre boutique a été soumise et est en attente de validation par notre équipe.');
    }

    public function pending(): View
    {
        $shop = Shop::where('user_id', auth()->id())->first();

        return view('seller.pending', compact('shop'));
    }
}
