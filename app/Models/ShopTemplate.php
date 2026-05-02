<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'icon', 'sections', 'fonts'];

    protected function casts(): array
    {
        return [
            'sections' => 'array',
            'fonts'    => 'array',
        ];
    }

    public function palettes()
    {
        return $this->hasMany(ShopPalette::class, 'template_id');
    }

    public function shops()
    {
        return $this->hasMany(Shop::class, 'template_id');
    }
}
