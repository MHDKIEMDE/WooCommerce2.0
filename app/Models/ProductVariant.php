<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'name', 'sku', 'price_modifier', 'stock_quantity', 'attributes', 'is_active'];

    protected function casts(): array
    {
        return [
            'attributes'     => 'array',
            'price_modifier' => 'decimal:2',
            'is_active'      => 'boolean',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
