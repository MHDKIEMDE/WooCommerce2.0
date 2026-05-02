<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'shop_id', 'order_number', 'status',
        'subtotal', 'shipping_cost', 'tax_amount', 'discount_amount', 'total',
        'shipping_address', 'billing_address',
        'payment_method', 'payment_status', 'payment_reference',
        'coupon_id', 'tracking_number', 'notes',
        'shipped_at', 'delivered_at', 'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'shipping_address' => 'array',
            'billing_address'  => 'array',
            'subtotal'         => 'decimal:2',
            'shipping_cost'    => 'decimal:2',
            'tax_amount'       => 'decimal:2',
            'discount_amount'  => 'decimal:2',
            'total'            => 'decimal:2',
            'shipped_at'       => 'datetime',
            'delivered_at'     => 'datetime',
            'cancelled_at'     => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
