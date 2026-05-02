<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProductObserver
{
    public function creating(Product $product): void
    {
        $product->slug = $this->uniqueSlug($product->name, $product->id);
    }

    public function updating(Product $product): void
    {
        if ($product->isDirty('name') && empty($product->slug)) {
            $product->slug = $this->uniqueSlug($product->name, $product->id);
        }
    }

    public function saved(Product $product): void
    {
        Cache::forget("product:{$product->slug}");
        Cache::forget('marketplace:home');
    }

    public function deleted(Product $product): void
    {
        Cache::forget("product:{$product->slug}");
        Cache::forget('marketplace:home');
    }

    private function uniqueSlug(string $name, ?int $excludeId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;

        while (
            Product::where('slug', $slug)
                ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
