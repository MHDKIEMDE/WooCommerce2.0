<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'variant_id',
        'product_name', 'product_sku', 'quantity',
        'unit_price', 'total_price', 'vat_rate', 'product_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'product_snapshot' => 'array',
            'unit_price'       => 'decimal:2',
            'total_price'      => 'decimal:2',
            'vat_rate'         => 'decimal:2',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
