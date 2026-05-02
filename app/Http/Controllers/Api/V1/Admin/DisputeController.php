<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Events\DisputeResolved;
use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\Dispute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DisputeController extends BaseApiController
{
    // GET /api/v1/admin/disputes
    public function index(Request $request): JsonResponse
    {
        $query = Dispute::with([
            'user:id,name,email',
            'order:id,order_number,total,shop_id',
            'order.shop:id,name,slug',
        ])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('order', fn ($q) => $q->where('order_number', 'like', "%{$s}%"))
                  ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$s}%"));
        }

        return $this->paginated($query->paginate(20));
    }

    // GET /api/v1/admin/disputes/{id}
    public function show(int $id): JsonResponse
    {
        $dispute = Dispute::with([
            'user:id,name,email',
            'order:id,order_number,total,status,shop_id',
            'order.shop:id,name,slug',
            'order.shop.owner:id,name,email',
            'messages.user:id,name,role',
        ])->findOrFail($id);

        return $this->success($dispute);
    }

    // POST /api/v1/admin/disputes/{id}/messages
    public function addMessage(Request $request, int $id): JsonResponse
    {
        $dispute = Dispute::findOrFail($id);

        if (! $dispute->isOpen()) {
            return $this->error('Ce litige est clôturé.', 403);
        }

        $data = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $message = $dispute->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $data['message'],
        ]);

        return $this->success($message->load('user:id,name,role'), 'Message ajouté.', 201);
    }

    // PATCH /api/v1/admin/disputes/{id}/resolve
    public function resolve(Request $request, int $id): JsonResponse
    {
        $dispute = Dispute::with(['order.shop.owner', 'user'])->findOrFail($id);

        $data = $request->validate([
            'resolution_note' => 'required|string|max:2000',
            'refund_issued'   => 'boolean',
        ]);

        $dispute->update([
            'status'          => 'resolved',
            'resolution_note' => $data['resolution_note'],
            'refund_issued'   => $data['refund_issued'] ?? false,
            'resolved_at'     => now(),
        ]);

        event(new DisputeResolved($dispute));

        return $this->success($dispute, 'Litige résolu. Acheteur et vendeur ont été notifiés.');
    }

    // PATCH /api/v1/admin/disputes/{id}/close
    public function close(int $id): JsonResponse
    {
        $dispute = Dispute::findOrFail($id);

        $dispute->update([
            'status'      => 'closed',
            'resolved_at' => now(),
        ]);

        return $this->success($dispute, 'Litige clôturé.');
    }
}
