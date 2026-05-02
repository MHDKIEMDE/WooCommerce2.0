<?php

namespace App\Http\Controllers\Api\V1\Seller;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\Dispute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SellerDisputeController extends BaseApiController
{
    // GET /api/v1/seller/disputes
    public function index(Request $request): JsonResponse
    {
        $shop = $request->user()->shop;

        if (! $shop) {
            return $this->error('Aucune boutique associée.', 404);
        }

        $query = Dispute::with(['user:id,name,email', 'order:id,order_number,total'])
            ->whereHas('order', fn ($q) => $q->where('shop_id', $shop->id))
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $this->paginated($query->paginate(20));
    }

    // GET /api/v1/seller/disputes/{id}
    public function show(Request $request, int $id): JsonResponse
    {
        $shop = $request->user()->shop;

        if (! $shop) {
            return $this->error('Aucune boutique associée.', 404);
        }

        $dispute = Dispute::with([
            'user:id,name,email',
            'order:id,order_number,total,status',
            'messages.user:id,name,role',
        ])
        ->whereHas('order', fn ($q) => $q->where('shop_id', $shop->id))
        ->findOrFail($id);

        return $this->success($dispute);
    }

    // POST /api/v1/seller/disputes/{id}/messages
    public function addMessage(Request $request, int $id): JsonResponse
    {
        $shop = $request->user()->shop;

        if (! $shop) {
            return $this->error('Aucune boutique associée.', 404);
        }

        $dispute = Dispute::whereHas('order', fn ($q) => $q->where('shop_id', $shop->id))
            ->findOrFail($id);

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
}
