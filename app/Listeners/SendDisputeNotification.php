<?php

namespace App\Listeners;

use App\Events\DisputeOpened;
use App\Events\DisputeResolved;
use App\Models\User;
use App\Notifications\DisputeResolvedNotification;
use App\Notifications\NewDisputeNotification;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class SendDisputeNotification implements ShouldHandleEventsAfterCommit
{
    public function handle(object $event): void
    {
        $dispute = $event->dispute->load(['order.shop.owner', 'user']);

        if ($event instanceof DisputeOpened) {
            $notification = new NewDisputeNotification($dispute);

            // Notifier le vendeur de la boutique concernée
            $seller = $dispute->order?->shop?->owner;
            if ($seller) {
                $seller->notify($notification);
            }

            // Notifier tous les admins
            User::where('role', 'admin')->each(fn ($admin) => $admin->notify($notification));
        }

        if ($event instanceof DisputeResolved) {
            $notification = new DisputeResolvedNotification($dispute);

            // Notifier l'acheteur
            $dispute->user->notify($notification);

            // Notifier le vendeur
            $seller = $dispute->order?->shop?->owner;
            if ($seller) {
                $seller->notify($notification);
            }
        }
    }
}
