<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_order',
        'usage_limit', 'used_count', 'expires_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value'      => 'decimal:2',
            'min_order'  => 'decimal:2',
            'is_active'  => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    public function isValid(float $orderTotal = 0): bool
    {
        if (! $this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        if ($this->min_order && $orderTotal < $this->min_order) return false;

        return true;
    }
}
