<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function index(Request $request): View
    {
        $cart = $this->cartService->getCart($request->user());

        return view('cart', [
            'items'   => $cart['items'],
            'totals'  => $cart['totals'],
            'coupon'  => $cart['coupon'],
            'count'   => $cart['count'],
        ]);
    }

    public function add(Request $request): RedirectResponse
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
            return back()->withErrors(['cart' => $result['message']]);
        }

        return back()->with('success', $result['message']);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:99']]);

        $result = $this->cartService->updateItem($request->user(), $id, $request->quantity);

        if (! $result['success']) {
            return back()->withErrors(['cart' => $result['message']]);
        }

        return back()->with('success', $result['message']);
    }

    public function remove(Request $request, int $id): RedirectResponse
    {
        $this->cartService->removeItem($request->user(), $id);

        return back()->with('success', 'Article supprimé du panier.');
    }

    public function applyCoupon(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|string|max:50']);

        $result = $this->cartService->applyCoupon($request->user(), $request->code);

        return $result['valid']
            ? back()->with('success', $result['message'])
            : back()->withErrors(['coupon' => $result['message']]);
    }

    public function removeCoupon(): RedirectResponse
    {
        $this->cartService->removeCoupon();

        return back()->with('success', 'Code promo retiré.');
    }
}
