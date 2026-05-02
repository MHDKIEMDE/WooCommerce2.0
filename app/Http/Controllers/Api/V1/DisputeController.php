<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\DisputeOpened;
use App\Models\Dispute;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DisputeController extends BaseApiController
{
    // GET /api/v1/disputes
    public function index(Request $request): JsonResponse
    {
        $disputes = Dispute::with(['order:id,order_number,total,status', 'messages' => fn ($q) => $q->latest()->limit(1)])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return $this->paginated($disputes);
    }

    // GET /api/v1/disputes/{id}
    public function show(Request $request, int $id): JsonResponse
    {
        $dispute = Dispute::with(['order:id,order_number,total,status,shop_id', 'order.shop:id,name,slug', 'messages.user:id,name,role'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return $this->success($dispute);
    }

    // POST /api/v1/disputes
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'order_id'    => 'required|exists:orders,id',
            'reason'      => 'required|string|max:191',
            'description' => 'nullable|string|max:2000',
        ]);

        $order = Order::where('id', $data['order_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        // Un seul litige actif par commande
        if (Dispute::where('order_id', $order->id)->whereIn('status', ['open', 'pending'])->exists()) {
            return $this->error('Un litige est déjà en cours pour cette commande.', 409);
        }

        $dispute = Dispute::create([
            'order_id'    => $order->id,
            'user_id'     => $request->user()->id,
            'reason'      => $data['reason'],
            'description' => $data['description'] ?? null,
            'status'      => 'open',
        ]);

        event(new DisputeOpened($dispute->load(['order', 'user'])));

        return $this->success($dispute, 'Litige ouvert. Le vendeur et l\'administrateur ont été notifiés.', 201);
    }

    // POST /api/v1/disputes/{id}/messages
    public function addMessage(Request $request, int $id): JsonResponse
    {
        $dispute = Dispute::where('user_id', $request->user()->id)->findOrFail($id);

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

        // Passer en "pending" si l'acheteur répond après ouverture
        if ($dispute->status === 'open') {
            $dispute->update(['status' => 'pending']);
        }

        return $this->success($message->load('user:id,name,role'), 'Message ajouté.', 201);
    }
}
