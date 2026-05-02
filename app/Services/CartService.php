<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function __construct(
        private StockService  $stockService,
        private CouponService $couponService,
    ) {}

    // ── Récupération du panier ────────────────────────────────────────────

    public function getCart(?User $user): array
    {
        $items = $this->getItems($user);

        return $this->buildCartData($items, $user);
    }

    public function getItems(?User $user): Collection
    {
        if ($user) {
            return CartItem::with(['product.images', 'variant'])
                ->where('user_id', $user->id)
                ->get();
        }

        return CartItem::with(['product.images', 'variant'])
            ->where('session_id', Session::getId())
            ->whereNull('user_id')
            ->get();
    }

    // ── Ajout d'un article ────────────────────────────────────────────────

    public function addItem(?User $user, int $productId, int $quantity = 1, ?int $variantId = null): array
    {
        $product = Product::findOrFail($productId);

        if ($product->status !== 'active') {
            return ['success' => false, 'message' => 'Produit indisponible.'];
        }

        if (! $this->stockService->check($productId, $quantity, $variantId)) {
            return ['success' => false, 'message' => 'Stock insuffisant.'];
        }

        $existing = $this->findItem($user, $productId, $variantId);

        if ($existing) {
            $newQty = $existing->quantity + $quantity;
            if (! $this->stockService->check($productId, $newQty, $variantId)) {
                return ['success' => false, 'message' => 'Stock insuffisant pour cette quantité.'];
            }
            $existing->update(['quantity' => $newQty]);
        } else {
            CartItem::create([
                'user_id'    => $user?->id,
                'session_id' => $user ? null : Session::getId(),
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity'   => $quantity,
            ]);
        }

        return ['success' => true, 'message' => 'Article ajouté au panier.'];
    }

    // ── Mise à jour de la quantité ────────────────────────────────────────

    public function updateItem(?User $user, int $itemId, int $quantity): array
    {
        $item = $this->findItemById($user, $itemId);

        if (! $item) {
            return ['success' => false, 'message' => 'Article introuvable.'];
        }

        if (! $this->stockService->check($item->product_id, $quantity, $item->variant_id)) {
            return ['success' => false, 'message' => 'Stock insuffisant.'];
        }

        $item->update(['quantity' => $quantity]);

        return ['success' => true, 'message' => 'Quantité mise à jour.'];
    }

    // ── Suppression d'un article ──────────────────────────────────────────

    public function removeItem(?User $user, int $itemId): bool
    {
        $item = $this->findItemById($user, $itemId);

        if (! $item) return false;

        $item->delete();
        return true;
    }

    // ── Vider le panier ───────────────────────────────────────────────────

    public function clear(?User $user): void
    {
        if ($user) {
            CartItem::where('user_id', $user->id)->delete();
        } else {
            CartItem::where('session_id', Session::getId())->whereNull('user_id')->delete();
        }

        Session::forget('cart_coupon');
    }

    // ── Coupon ────────────────────────────────────────────────────────────

    public function applyCoupon(?User $user, string $code): array
    {
        $items    = $this->getItems($user);
        $subtotal = $this->calculateSubtotal($items);

        $result = $this->couponService->validate($code, $subtotal);

        if ($result['valid']) {
            Session::put('cart_coupon', strtoupper($code));
        }

        return $result;
    }

    public function removeCoupon(): void
    {
        Session::forget('cart_coupon');
    }

    // ── Calcul des totaux ─────────────────────────────────────────────────

    public function calculateTotals(Collection $items, ?Coupon $coupon = null, float $shippingCost = 0.0): array
    {
        $subtotal  = $this->calculateSubtotal($items);
        $discount  = $coupon ? $this->couponService->calculateDiscount($coupon, $subtotal) : 0.0;
        $taxBase   = $subtotal - $discount;
        $taxAmount = round($taxBase * 0.20, 2);
        $total     = round($taxBase + $shippingCost, 2);

        return compact('subtotal', 'shippingCost', 'discount', 'taxAmount', 'total');
    }

    // ── Fusion panier invité → connecté ───────────────────────────────────

    public function mergeGuestCart(User $user): void
    {
        $sessionId = Session::getId();

        $guestItems = CartItem::where('session_id', $sessionId)->whereNull('user_id')->get();

        foreach ($guestItems as $guestItem) {
            $existing = CartItem::where('user_id', $user->id)
                ->where('product_id', $guestItem->product_id)
                ->where('variant_id', $guestItem->variant_id)
                ->first();

            if ($existing) {
                $existing->increment('quantity', $guestItem->quantity);
                $guestItem->delete();
            } else {
                $guestItem->update(['user_id' => $user->id, 'session_id' => null]);
            }
        }
    }

    // ── Helpers privés ────────────────────────────────────────────────────

    private function findItem(?User $user, int $productId, ?int $variantId): ?CartItem
    {
        $query = CartItem::where('product_id', $productId)
            ->where('variant_id', $variantId);

        return $user
            ? $query->where('user_id', $user->id)->first()
            : $query->where('session_id', Session::getId())->whereNull('user_id')->first();
    }

    private function findItemById(?User $user, int $itemId): ?CartItem
    {
        $query = CartItem::where('id', $itemId);

        return $user
            ? $query->where('user_id', $user->id)->first()
            : $query->where('session_id', Session::getId())->whereNull('user_id')->first();
    }

    private function calculateSubtotal(Collection $items): float
    {
        return round($items->sum(function (CartItem $item) {
            $price = $item->product->price ?? 0;
            if ($item->variant && $item->variant->price_modifier) {
                $price += $item->variant->price_modifier;
            }
            return $price * $item->quantity;
        }), 2);
    }

    // ── Grouper les articles par boutique ─────────────────────────────────

    public function groupByShop(Collection $items): array
    {
        return $items->groupBy(fn ($item) => $item->product?->shop_id ?? 0)
            ->map(function ($shopItems, $shopId) {
                $shop     = $shopItems->first()?->product?->shop;
                $subtotal = round($shopItems->sum(function ($item) {
                    $price = (float)($item->product->price ?? 0);
                    if ($item->variant?->price_modifier) $price += (float)$item->variant->price_modifier;
                    return $price * $item->quantity;
                }), 2);

                return [
                    'shop'     => $shop ? ['id' => $shop->id, 'name' => $shop->name, 'slug' => $shop->slug] : null,
                    'items'    => $shopItems->values(),
                    'subtotal' => $subtotal,
                ];
            })
            ->values()
            ->toArray();
    }

    private function buildCartData(Collection $items, ?User $user): array
    {
        $couponCode = Session::get('cart_coupon');
        $coupon     = $couponCode ? Coupon::where('code', $couponCode)->first() : null;
        $totals     = $this->calculateTotals($items, $coupon);

        return [
            'items'       => $items,
            'by_shop'     => $this->groupByShop($items),
            'coupon'      => $coupon,
            'totals'      => $totals,
            'count'       => $items->sum('quantity'),
            'shop_count'  => $items->groupBy(fn ($i) => $i->product?->shop_id ?? 0)->count(),
        ];
    }
}
