<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\ShopCreated;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use App\Models\ShopPalette;
use App\Models\ShopTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShopController extends BaseApiController
{
    // GET /api/v1/templates
    public function templates(): JsonResponse
    {
        $templates = ShopTemplate::with('palettes')->get();

        return $this->success($templates->map(fn ($t) => [
            'id'       => $t->id,
            'name'     => $t->name,
            'slug'     => $t->slug,
            'icon'     => $t->icon,
            'sections' => $t->sections,
            'fonts'    => $t->fonts,
            'palettes' => $t->palettes->map(fn ($p) => [
                'id'            => $p->id,
                'name'          => $p->name,
                'color_primary' => $p->color_primary,
                'color_accent'  => $p->color_accent,
                'color_bg'      => $p->color_bg,
                'color_text'    => $p->color_text,
                'ambiance'      => $p->ambiance,
            ]),
        ]));
    }

    // GET /api/v1/templates/{slug}/palettes
    public function palettes(string $slug): JsonResponse
    {
        $template = ShopTemplate::where('slug', $slug)->firstOrFail();

        return $this->success($template->palettes);
    }

    // GET /api/v1/shops/{slug}
    public function show(string $slug): JsonResponse
    {
        $shop = Shop::where('slug', $slug)
            ->where('status', 'active')
            ->with(['template', 'palette', 'sections' => fn ($q) => $q->where('is_active', true)])
            ->firstOrFail();

        return $this->success(new ShopResource($shop));
    }

    // POST /api/v1/shops
    public function store(Request $request): JsonResponse
    {
        if (! $request->user()->isSeller()) {
            return $this->error('Seuls les vendeurs peuvent créer une boutique.', 403);
        }

        if ($request->user()->shop()->exists()) {
            return $this->error('Vous avez déjà une boutique.', 409);
        }

        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'template_id' => 'required|exists:shop_templates,id',
            'palette_id'  => 'required|exists:shop_palettes,id',
            'description' => 'nullable|string|max:1000',
            'font'        => 'nullable|string|max:100',
        ]);

        // Valider que la palette appartient au template
        $palette = ShopPalette::where('id', $data['palette_id'])
            ->where('template_id', $data['template_id'])
            ->firstOrFail();

        $slug = $this->uniqueSlug($data['name']);

        $shop = Shop::create([
            'user_id'     => $request->user()->id,
            'template_id' => $data['template_id'],
            'palette_id'  => $palette->id,
            'name'        => $data['name'],
            'slug'        => $slug,
            'subdomain'   => $slug,
            'description' => $data['description'] ?? null,
            'font'        => $data['font'] ?? null,
            'status'      => 'pending',
        ]);

        // Créer les sections par défaut du template
        $template = ShopTemplate::find($data['template_id']);
        if ($template && $template->sections) {
            foreach ($template->sections as $i => $sectionType) {
                $shop->sections()->create([
                    'type'       => $sectionType,
                    'is_active'  => true,
                    'sort_order' => $i,
                ]);
            }
        }

        event(new ShopCreated($shop->load(['owner', 'template'])));

        return $this->success(
            new ShopResource($shop->load(['template', 'palette', 'sections'])),
            'Boutique créée. En attente de validation par l\'administrateur.',
            201
        );
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;

        while (Shop::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
