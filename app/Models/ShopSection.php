<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopSection extends Model
{
    protected $fillable = ['shop_id', 'type', 'content', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return [
            'content'   => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
