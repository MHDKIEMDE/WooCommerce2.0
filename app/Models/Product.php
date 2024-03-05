<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * Obtenez la catégorie associée à ce produit.
     */

    public function category()
    {
        return $this->belongsTo(Categorie::class, 'category_id');
    }

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'price', 'category_id',
    ];

    /**
     * Obtenez les images associées à ce produit.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }


}
