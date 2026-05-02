<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopPalette extends Model
{
    protected $fillable = [
        'template_id', 'name',
        'color_primary', 'color_accent', 'color_bg', 'color_text', 'ambiance',
    ];

    public function template()
    {
        return $this->belongsTo(ShopTemplate::class, 'template_id');
    }
}
