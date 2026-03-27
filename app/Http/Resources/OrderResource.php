<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'order_number'     => $this->order_number,
            'status'           => $this->status,
            'subtotal'         => $this->subtotal,
            'shipping_cost'    => $this->shipping_cost,
            'tax_amount'       => $this->tax_amount,
            'discount_amount'  => $this->discount_amount,
            'total'            => $this->total,
            'shipping_address' => $this->shipping_address,
            'billing_address'  => $this->billing_address,
            'payment_method'   => $this->payment_method,
            'payment_status'   => $this->payment_status,
            'tracking_number'  => $this->tracking_number,
            'notes'            => $this->notes,
            'shipped_at'       => $this->shipped_at?->toIso8601String(),
            'delivered_at'     => $this->delivered_at?->toIso8601String(),
            'cancelled_at'     => $this->cancelled_at?->toIso8601String(),
            'created_at'       => $this->created_at?->toIso8601String(),
            'items'            => OrderItemResource::collection($this->whenLoaded('items')),
            'coupon_code'      => $this->coupon?->code,
        ];
    }
}
