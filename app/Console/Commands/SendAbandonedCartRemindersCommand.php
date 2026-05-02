<?php

namespace App\Console\Commands;

use App\Models\AbandonedCart;
use App\Notifications\AbandonedCartNotification;
use Illuminate\Console\Command;

class SendAbandonedCartRemindersCommand extends Command
{
    protected $signature   = 'carts:send-reminders';
    protected $description = 'Envoie email + push aux utilisateurs avec un panier abandonné depuis 24h';

    public function handle(): int
    {
        $carts = AbandonedCart::with('user')
            ->whereNull('notified_at')
            ->where('last_activity_at', '<=', now()->subHours(24))
            ->get();

        if ($carts->isEmpty()) {
            $this->info('Aucun panier abandonné à notifier.');
            return self::SUCCESS;
        }

        $sent = 0;

        foreach ($carts as $cart) {
            $user = $cart->user;

            if (! $user || ! $user->is_active) {
                continue;
            }

            $notification = new AbandonedCartNotification($cart);

            // Email via Laravel Notifications (SMTP Gmail)
            $user->notify($notification);

            // Push FCM séparé
            $notification->sendPush($user);

            $cart->update(['notified_at' => now()]);
            $sent++;
        }

        $this->info("Relances panier abandonné envoyées : {$sent}");

        return self::SUCCESS;
    }
}
