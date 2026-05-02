<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Shop extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'template_id', 'palette_id', 'name', 'slug', 'subdomain',
        'logo', 'banner', 'font', 'layout', 'status', 'description',
        'stripe_account_id', 'commission_rate',
    ];

    protected function casts(): array
    {
        return [
            'commission_rate' => 'decimal:2',
        ];
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function template()
    {
        return $this->belongsTo(ShopTemplate::class, 'template_id');
    }

    public function palette()
    {
        return $this->belongsTo(ShopPalette::class, 'palette_id');
    }

    public function sections()
    {
        return $this->hasMany(ShopSection::class)->orderBy('sort_order');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? Storage::url($this->logo) : null;
    }

    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner ? Storage::url($this->banner) : null;
    }
}
