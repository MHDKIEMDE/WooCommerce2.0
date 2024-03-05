<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class addToCart extends Model
{
    use HasFactory;

    /**
     * Les produits dans ce panier.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'cart_product', 'cart_id', 'product_id')->withPivot('quantity');
    }
}
