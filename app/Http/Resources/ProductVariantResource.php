<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'sku'            => $this->sku,
            'price_modifier' => $this->price_modifier,
            'stock_quantity' => $this->stock_quantity,
            'attributes'     => $this->attributes,
            'is_active'      => $this->is_active,
        ];
    }
}
