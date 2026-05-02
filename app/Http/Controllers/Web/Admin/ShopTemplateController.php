<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopPalette;
use App\Models\ShopTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShopTemplateController extends Controller
{
    public function index(): View
    {
        $templates = ShopTemplate::withCount(['palettes', 'shops'])->get();
        return view('dashboard.admin.Templates.index', compact('templates'));
    }

    public function create(): View
    {
        return view('dashboard.admin.Templates.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:shop_templates,name',
            'icon' => 'nullable|string|max:10',
        ]);

        ShopTemplate::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'icon' => $data['icon'] ?? null,
        ]);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template créé avec succès.');
    }

    public function edit(ShopTemplate $template): View
    {
        $template->load('palettes');
        return view('dashboard.admin.Templates.edit', compact('template'));
    }

    public function update(Request $request, ShopTemplate $template): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:shop_templates,name,' . $template->id,
            'icon' => 'nullable|string|max:10',
        ]);

        $template->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'icon' => $data['icon'] ?? null,
        ]);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template mis à jour.');
    }

    public function destroy(ShopTemplate $template): RedirectResponse
    {
        if ($template->shops()->exists()) {
            return back()->withErrors(['delete' => 'Impossible de supprimer : des boutiques utilisent ce template.']);
        }

        $template->palettes()->delete();
        $template->delete();

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template supprimé.');
    }

    // ── Palettes ─────────────────────────────────────────────────────────────

    public function storePalette(Request $request, ShopTemplate $template): RedirectResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'color_primary' => 'required|string|size:7',
            'color_accent'  => 'required|string|size:7',
            'color_bg'      => 'required|string|size:7',
            'color_text'    => 'required|string|size:7',
            'ambiance'      => 'nullable|string|max:100',
        ]);

        $template->palettes()->create($data);

        return back()->with('success', 'Palette ajoutée.');
    }

    public function destroyPalette(ShopTemplate $template, ShopPalette $palette): RedirectResponse
    {
        abort_unless($palette->template_id === $template->id, 403);
        $palette->delete();
        return back()->with('success', 'Palette supprimée.');
    }
}
