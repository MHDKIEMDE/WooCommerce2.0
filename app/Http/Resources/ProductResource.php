<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $primaryImage = $this->whenLoaded('images', function () {
            $primary = $this->images->firstWhere('is_primary', true) ?? $this->images->first();
            return $primary ? url($primary->url) : null;
        });

        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'short_description' => $this->short_description,
            'description'       => $this->when(
                $request->routeIs('*.products.show'),
                $this->description
            ),
            'price'             => $this->price,
            'compare_price'     => $this->compare_price,
            'discount_percent'  => $this->compare_price && $this->compare_price > $this->price
                ? round((($this->compare_price - $this->price) / $this->compare_price) * 100)
                : null,
            'sku'               => $this->sku,
            'stock_quantity'    => $this->stock_quantity,
            'in_stock'          => $this->stock_quantity > 0,
            'unit'              => $this->unit,
            'weight'            => $this->weight,
            'vat_rate'          => $this->vat_rate,
            'featured'          => $this->featured,
            'status'            => $this->status,
            'rating_avg'        => $this->rating_avg,
            'rating_count'      => $this->rating_count,
            'primary_image'     => $primaryImage,
            'category'          => new CategoryResource($this->whenLoaded('category')),
            'brand'             => new BrandResource($this->whenLoaded('brand')),
            'images'            => ProductImageResource::collection($this->whenLoaded('images')),
            'attributes'        => ProductAttributeResource::collection($this->whenLoaded('attributes')),
            'variants'          => ProductVariantResource::collection($this->whenLoaded('variants')),
            'meta_title'        => $this->when(
                $request->routeIs('*.products.show'),
                $this->meta_title
            ),
            'meta_description'  => $this->when(
                $request->routeIs('*.products.show'),
                $this->meta_description
            ),
            'created_at'        => $this->created_at?->toIso8601String(),
        ];
    }
}
