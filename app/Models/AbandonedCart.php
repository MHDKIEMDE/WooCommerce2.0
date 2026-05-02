<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbandonedCart extends Model
{
    protected $fillable = ['user_id', 'items', 'total', 'last_activity_at', 'notified_at'];

    protected function casts(): array
    {
        return [
            'items'            => 'array',
            'total'            => 'decimal:2',
            'last_activity_at' => 'datetime',
            'notified_at'      => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
