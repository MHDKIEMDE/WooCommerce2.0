<?php

namespace App\Listeners;

use App\Events\OrderCancelled;
use App\Events\OrderDelivered;
use App\Events\OrderPlaced;
use App\Events\OrderShipped;
use App\Notifications\OrderCancelledNotification;
use App\Notifications\OrderDeliveredNotification;
use App\Notifications\OrderPlacedNotification;
use App\Notifications\OrderShippedNotification;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class SendOrderNotification implements ShouldHandleEventsAfterCommit
{
    public function handle(object $event): void
    {
        $order = $event->order;
        $user  = $order->user;

        if (! $user) {
            return;
        }

        $notification = match (true) {
            $event instanceof OrderPlaced    => new OrderPlacedNotification($order),
            $event instanceof OrderShipped   => new OrderShippedNotification($order),
            $event instanceof OrderDelivered => new OrderDeliveredNotification($order),
            $event instanceof OrderCancelled => new OrderCancelledNotification($order),
            default                          => null,
        };

        if ($notification) {
            $user->notify($notification);
        }
    }
}
