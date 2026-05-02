<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'subdomain'   => $this->subdomain,
            'description' => $this->description,
            'status'      => $this->status,
            'logo'        => $this->logo_url,
            'banner'      => $this->banner_url,
            'font'        => $this->font,
            'layout'      => $this->layout,
            'template'    => $this->whenLoaded('template', fn () => [
                'id'       => $this->template->id,
                'name'     => $this->template->name,
                'slug'     => $this->template->slug,
                'icon'     => $this->template->icon,
                'sections' => $this->template->sections,
                'fonts'    => $this->template->fonts,
            ]),
            'palette'     => $this->whenLoaded('palette', fn () => [
                'id'            => $this->palette->id,
                'name'          => $this->palette->name,
                'color_primary' => $this->palette->color_primary,
                'color_accent'  => $this->palette->color_accent,
                'color_bg'      => $this->palette->color_bg,
                'color_text'    => $this->palette->color_text,
                'ambiance'      => $this->palette->ambiance,
            ]),
            'sections'    => $this->whenLoaded('sections', fn () =>
                $this->sections->map(fn ($s) => [
                    'type'       => $s->type,
                    'content'    => $s->content,
                    'is_active'  => $s->is_active,
                    'sort_order' => $s->sort_order,
                ])
            ),
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}
