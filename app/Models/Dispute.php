<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    protected $fillable = [
        'order_id', 'user_id', 'reason', 'description',
        'status', 'resolution_note', 'refund_issued', 'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'refund_issued' => 'boolean',
            'resolved_at'   => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(DisputeMessage::class)->orderBy('created_at');
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['open', 'pending']);
    }
}
