<?php

namespace App\Http\Controllers\Api\V1\Seller;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use App\Models\ShopPalette;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ShopAppearanceController extends BaseApiController
{
    // PATCH /api/v1/shops/{slug}/appearance
    public function update(Request $request, string $slug): JsonResponse
    {
        $shop = $request->attributes->get('shop') ?? Shop::where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'palette_id' => 'sometimes|exists:shop_palettes,id',
            'font'       => 'sometimes|nullable|string|max:100',
            'layout'     => 'sometimes|in:2,3,4,list',
            'banner'     => 'sometimes|nullable|image|max:2048',
            'logo'       => 'sometimes|nullable|image|max:1024',
        ]);

        // Valider que la palette appartient au template de la boutique
        if (isset($data['palette_id'])) {
            ShopPalette::where('id', $data['palette_id'])
                ->where('template_id', $shop->template_id)
                ->firstOrFail();
        }

        if ($request->hasFile('logo')) {
            if ($shop->logo) Storage::delete($shop->logo);
            $data['logo'] = $request->file('logo')->store("shops/{$shop->id}", 'public');
        }

        if ($request->hasFile('banner')) {
            if ($shop->banner) Storage::delete($shop->banner);
            $data['banner'] = $request->file('banner')->store("shops/{$shop->id}", 'public');
        }

        $shop->update($data);

        return $this->success(
            new ShopResource($shop->load(['template', 'palette'])),
            'Apparence mise à jour.'
        );
    }

    // PATCH /api/v1/shops/{slug}/sections
    public function updateSections(Request $request, string $slug): JsonResponse
    {
        $shop = $request->attributes->get('shop') ?? Shop::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'sections'             => 'required|array',
            'sections.*.type'      => 'required|string|max:50',
            'sections.*.is_active' => 'required|boolean',
            'sections.*.content'   => 'nullable|array',
            'sections.*.sort_order'=> 'nullable|integer|min:0',
        ]);

        foreach ($validated['sections'] as $i => $sectionData) {
            $shop->sections()->updateOrCreate(
                ['type' => $sectionData['type']],
                [
                    'is_active'  => $sectionData['is_active'],
                    'content'    => $sectionData['content'] ?? null,
                    'sort_order' => $sectionData['sort_order'] ?? $i,
                ]
            );
        }

        return $this->success(
            $shop->sections()->get(),
            'Sections mises à jour.'
        );
    }
}
