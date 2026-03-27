<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()
            ->orders()
            ->with(['items', 'coupon'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return $this->paginated($orders);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $order = $request->user()
            ->orders()
            ->with(['items', 'coupon'])
            ->find($id);

        if (! $order) {
            return $this->error('Commande introuvable.', 404);
        }

        return $this->success(new OrderResource($order));
    }

    public function invoice(Request $request, int $id): Response
    {
        $order = $request->user()
            ->orders()
            ->with(['items', 'coupon', 'user'])
            ->findOrFail($id);

        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            abort(501, 'DomPDF non installé. Exécutez : composer require barryvdh/laravel-dompdf');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', compact('order'));

        return $pdf->download("facture-{$order->order_number}.pdf");
    }
}
