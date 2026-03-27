<?php

namespace App\Listeners;

use App\Services\PushNotificationService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Support\Facades\Log;

class SendPushNotification implements ShouldHandleEventsAfterCommit
{
    public function __construct(private PushNotificationService $push) {}

    public function handle(object $event): void
    {
        $order = $event->order;
        $user  = $order->user;

        if (! $user) {
            return;
        }

        // Each order notification class exposes fcmPayload() for push data
        $notificationClass = $this->resolveNotificationClass($event);

        if (! $notificationClass) {
            return;
        }

        try {
            $notification = new $notificationClass($order);
            $payload      = $notification->fcmPayload();

            $this->push->sendToUser(
                $user,
                $payload['title'],
                $payload['body'],
                $payload['data'] ?? [],
            );
        } catch (\Throwable $e) {
            Log::error('SendPushNotification failed', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    private function resolveNotificationClass(object $event): ?string
    {
        $map = [
            \App\Events\OrderPlaced::class       => \App\Notifications\OrderPlacedNotification::class,
            \App\Events\OrderShipped::class      => \App\Notifications\OrderShippedNotification::class,
            \App\Events\OrderDelivered::class    => \App\Notifications\OrderDeliveredNotification::class,
            \App\Events\OrderCancelled::class    => \App\Notifications\OrderCancelledNotification::class,
        ];

        return $map[get_class($event)] ?? null;
    }
}
