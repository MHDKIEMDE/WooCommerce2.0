<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Events\OrderCancelled;
use App\Events\OrderDelivered;
use App\Events\OrderShipped;
use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Order::with('user:id,name,email')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) =>
                $q->where('order_number', 'like', "%{$s}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"))
            );
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        return $this->paginated($query->paginate(25));
    }

    public function show(int $id): JsonResponse
    {
        $order = Order::with(['user', 'items.product', 'coupon'])->findOrFail($id);

        return $this->success(new OrderResource($order));
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'status'          => 'required|in:pending,processing,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string|max:191',
            'notes'           => 'nullable|string',
        ]);

        $order = Order::findOrFail($id);

        $timestamps = [
            'shipped'   => 'shipped_at',
            'delivered' => 'delivered_at',
            'cancelled' => 'cancelled_at',
        ];

        if (isset($timestamps[$data['status']]) && ! $order->{$timestamps[$data['status']]}) {
            $data[$timestamps[$data['status']]] = now();
        }

        $order->update($data);

        // Fire domain event for the new status
        match ($data['status']) {
            'shipped'   => OrderShipped::dispatch($order),
            'delivered' => OrderDelivered::dispatch($order),
            'cancelled' => OrderCancelled::dispatch($order),
            default     => null,
        };

        return $this->success(null, 'Statut mis à jour.');
    }

    public function export(Request $request): JsonResponse
    {
        $query = Order::with('user:id,name,email')->latest();

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $orders = $query->get([
            'id', 'order_number', 'user_id', 'status', 'payment_status',
            'subtotal', 'discount_amount', 'shipping_cost', 'tax_amount', 'total',
            'created_at',
        ]);

        return $this->success($orders);
    }
}
