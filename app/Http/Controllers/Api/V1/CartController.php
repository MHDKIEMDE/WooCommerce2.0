<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\CartResource;
use App\Services\CartService;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends BaseApiController
{
    public function __construct(
        private CartService $cartService,
        private CouponService $couponService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $cart = $this->cartService->getCart(Auth::guard('sanctum')->user());

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
            Auth::guard('sanctum')->user(),
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

        $result = $this->cartService->updateItem(Auth::guard('sanctum')->user(), $id, $request->quantity);

        if (! $result['success']) {
            return $this->error($result['message'], 422);
        }

        return $this->success(null, $result['message']);
    }

    public function removeItem(Request $request, int $id): JsonResponse
    {
        $removed = $this->cartService->removeItem(Auth::guard('sanctum')->user(), $id);

        if (! $removed) {
            return $this->error('Article introuvable.', 404);
        }

        return $this->success(null, 'Article supprimé.');
    }

    public function checkCoupon(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string|max:50']);

        $cart   = $this->cartService->getCart(Auth::guard('sanctum')->user());
        $result = $this->couponService->validate($request->code, $cart['totals']['subtotal']);

        if (! $result['valid']) {
            return $this->error($result['message'], 422);
        }

        return $this->success([
            'code'     => strtoupper($request->code),
            'type'     => $result['coupon']->type,
            'value'    => $result['coupon']->value,
            'discount' => $result['discount'],
        ], $result['message']);
    }

    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string|max:50']);

        $result = $this->cartService->applyCoupon(Auth::guard('sanctum')->user(), $request->code);

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
        $this->cartService->clear(Auth::guard('sanctum')->user());

        return $this->success(null, 'Panier vidé.');
    }
}
