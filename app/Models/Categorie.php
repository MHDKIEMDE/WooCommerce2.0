<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'image_path',
    ];

    /**
     * Obtenez les produits associés à cette catégorie.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
