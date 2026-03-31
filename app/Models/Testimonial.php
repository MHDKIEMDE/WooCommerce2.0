<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['name', 'profession', 'description', 'photo', 'rating', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->latest();
    }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? \Illuminate\Support\Facades\Storage::url($this->photo)
            : asset('img/testimonial-1.jpg');
    }
}
