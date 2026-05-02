<?php

namespace App\Listeners;

use App\Events\ShopApproved;
use App\Events\ShopCreated;
use App\Events\ShopSuspended;
use App\Notifications\ShopApprovedNotification;
use App\Notifications\ShopCreatedNotification;
use App\Notifications\ShopSuspendedNotification;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class SendShopNotification implements ShouldHandleEventsAfterCommit
{
    public function handle(object $event): void
    {
        $shop  = $event->shop;
        $owner = $shop->owner;

        if (! $owner) {
            return;
        }

        $notification = match (true) {
            $event instanceof ShopCreated   => new ShopCreatedNotification($shop),
            $event instanceof ShopApproved  => new ShopApprovedNotification($shop),
            $event instanceof ShopSuspended => new ShopSuspendedNotification($shop, $event->reason ?? null),
            default                         => null,
        };

        if ($notification) {
            $owner->notify($notification);
        }
    }
}
