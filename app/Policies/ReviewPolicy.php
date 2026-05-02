<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function before(User $user): ?bool
    {
        return $user->role === 'admin' ? true : null;
    }

    /** L'auteur peut supprimer son propre avis. */
    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->user_id;
    }

    /** Seul l'admin (géré par before) peut approuver. */
    public function approve(User $user, Review $review): bool
    {
        return false;
    }
}
