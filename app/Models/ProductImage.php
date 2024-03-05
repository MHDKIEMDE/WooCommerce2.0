<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id', 'image_path',
    ];

    /**
     * Obtenez le produit associé à cette image.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
