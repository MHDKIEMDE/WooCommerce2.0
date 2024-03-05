<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    /**
     * Run the migrations.
     */

     protected $fillable = [
        'name', 'desscription', 'profession',
    ];

    /**
     * Obtenez les produits associés à cette catégorie.
     */
    public function Users()
    {
        return $this->hasMany(User::class, 'testimonial_id');
    }
};
