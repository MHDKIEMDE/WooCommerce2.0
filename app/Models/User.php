<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable

{
use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_name', // Nouveau champ : nom de famille
        'profile_image', // Nouveau champ : image de profil
        'address', // Nouveau champ : adresse
        'quarter', // Nouveau champ : quartier
        'phone_number', // Nouveau champ : numéro de téléphone
        'secondary_phone_number', // Nouveau champ : deuxième numéro de téléphone
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function Testimonials()
    {
        return $this->belongsTo(Testimonial::class, 'testimonial_id');
    }
    
    public function Comment()
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }
}
