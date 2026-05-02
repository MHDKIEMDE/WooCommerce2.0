<?php

namespace App\Policies;

use App\Models\Shop;
use App\Models\User;

class ShopPolicy
{
    /** Admin peut tout faire. */
    public function before(User $user): ?bool
    {
        return $user->role === 'admin' ? true : null;
    }

    /** Seul le propriétaire peut voir le dashboard de sa boutique. */
    public function view(User $user, Shop $shop): bool
    {
        return $user->id === $shop->user_id;
    }

    /** Seul le propriétaire peut modifier sa boutique. */
    public function update(User $user, Shop $shop): bool
    {
        return $user->id === $shop->user_id;
    }

    /** Seul le propriétaire peut supprimer sa boutique. */
    public function delete(User $user, Shop $shop): bool
    {
        return $user->id === $shop->user_id;
    }

    /** Un vendeur peut créer une boutique si il n'en a pas déjà une. */
    public function create(User $user): bool
    {
        return in_array($user->role, ['seller', 'buyer'])
            && ! Shop::where('user_id', $user->id)->exists();
    }
}
