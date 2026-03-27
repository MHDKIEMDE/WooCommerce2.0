<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;

class StockService
{
    public function check(int $productId, int $quantity, ?int $variantId = null): bool
    {
        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            return $variant && $variant->is_active && $variant->stock_quantity >= $quantity;
        }

        $product = Product::find($productId);
        return $product && $product->stock_quantity >= $quantity;
    }

    public function decrement(int $productId, int $quantity, ?int $variantId = null): void
    {
        if ($variantId) {
            ProductVariant::where('id', $variantId)
                ->decrement('stock_quantity', $quantity);
        }

        Product::where('id', $productId)
            ->decrement('stock_quantity', $quantity);
    }

    public function restore(int $productId, int $quantity, ?int $variantId = null): void
    {
        if ($variantId) {
            ProductVariant::where('id', $variantId)
                ->increment('stock_quantity', $quantity);
        }

        Product::where('id', $productId)
            ->increment('stock_quantity', $quantity);
    }
}
