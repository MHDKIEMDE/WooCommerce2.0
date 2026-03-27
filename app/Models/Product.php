<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'short_description',
        'price', 'compare_price', 'cost_price', 'sku',
        'stock_quantity', 'low_stock_threshold',
        'category_id', 'brand_id', 'status', 'featured',
        'rating_avg', 'rating_count', 'weight', 'unit', 'vat_rate',
        'meta_title', 'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'price'         => 'decimal:2',
            'compare_price' => 'decimal:2',
            'cost_price'    => 'decimal:2',
            'rating_avg'    => 'decimal:2',
            'vat_rate'      => 'decimal:2',
            'featured'      => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class)->orderBy('sort_order');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }
}
