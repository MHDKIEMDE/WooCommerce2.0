<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $product = $this->product;
        $img     = $product?->images?->firstWhere('is_primary', true)
                ?? $product?->images?->first();

        return [
            'id'         => $this->id,
            'product_id' => $this->product_id,
            'variant_id' => $this->variant_id,
            'quantity'   => $this->quantity,
            'product'    => $product ? [
                'name'    => $product->name,
                'slug'    => $product->slug,
                'price'   => $product->price,
                'unit'    => $product->unit,
                'image'   => $img ? url($img->url) : null,
                'in_stock' => $product->stock_quantity > 0,
            ] : null,
            'variant'    => $this->variant ? [
                'name'           => $this->variant->name,
                'price_modifier' => $this->variant->price_modifier,
            ] : null,
            'line_total' => round(
                ((float)$product?->price + (float)($this->variant?->price_modifier ?? 0)) * $this->quantity,
                2
            ),
        ];
    }
}
