<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
   /**
     * Run the migrations.
     */

     protected $fillable = [
        'name', 'evaluation', 'comment', 'emial', 
    ];

    /**
     * Obtenez les produits associés à cette catégorie.
     */
    public function Users()
    {
        return $this->hasMany(User::class, 'comment_id');
    }
}
