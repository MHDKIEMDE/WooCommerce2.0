<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends BaseApiController
{
    public function __construct(private CartService $cartService) {}

    public function index(Request $request): JsonResponse
    {
        $cart = $this->cartService->getCart($request->user());

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => CartResource::collection($cart['items']),
            'meta'    => [
                'count'         => $cart['count'],
                'subtotal'      => $cart['totals']['subtotal'],
                'shipping_cost' => $cart['totals']['shippingCost'],
                'discount'      => $cart['totals']['discount'],
                'tax_amount'    => $cart['totals']['taxAmount'],
                'total'         => $cart['totals']['total'],
                'coupon'        => $cart['coupon']?->code,
            ],
        ]);
    }

    public function addItem(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['sometimes', 'integer', 'min:1', 'max:99'],
            'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
        ]);

        $result = $this->cartService->addItem(
            $request->user(),
            $request->product_id,
            $request->input('quantity', 1),
            $request->variant_id,
        );

        if (! $result['success']) {
            return $this->error($result['message'], 422);
        }

        return $this->success(null, $result['message'], 201);
    }

    public function updateItem(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $result = $this->cartService->updateItem($request->user(), $id, $request->quantity);

        if (! $result['success']) {
            return $this->error($result['message'], 422);
        }

        return $this->success(null, $result['message']);
    }

    public function removeItem(Request $request, int $id): JsonResponse
    {
        $removed = $this->cartService->removeItem($request->user(), $id);

        if (! $removed) {
            return $this->error('Article introuvable.', 404);
        }

        return $this->success(null, 'Article supprimé.');
    }

    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string|max:50']);

        $result = $this->cartService->applyCoupon($request->user(), $request->code);

        if (! $result['valid']) {
            return $this->error($result['message'], 422);
        }

        return $this->success(['discount' => $result['discount']], $result['message']);
    }

    public function removeCoupon(): JsonResponse
    {
        $this->cartService->removeCoupon();

        return $this->success(null, 'Code promo retiré.');
    }

    public function clear(Request $request): JsonResponse
    {
        $this->cartService->clear($request->user());

        return $this->success(null, 'Panier vidé.');
    }
}
