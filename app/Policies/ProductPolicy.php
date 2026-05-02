<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function before(User $user): ?bool
    {
        return $user->role === 'admin' ? true : null;
    }

    /** Le vendeur doit posséder la boutique du produit. */
    public function view(User $user, Product $product): bool
    {
        return $product->shop?->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'seller';
    }

    public function update(User $user, Product $product): bool
    {
        return $product->shop?->user_id === $user->id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $product->shop?->user_id === $user->id;
    }
}
