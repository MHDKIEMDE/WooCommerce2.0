<?php

namespace App\Notifications;

use App\Models\AbandonedCart;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AbandonedCartNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly AbandonedCart $cart) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $itemCount = count($this->cart->items);
        $total     = number_format((float) $this->cart->total, 2, ',', ' ');

        $mail = (new MailMessage)
            ->subject("Vous avez oublié quelque chose dans votre panier 🛒")
            ->greeting("Bonjour {$notifiable->name} !")
            ->line("Vous avez laissé **{$itemCount} article(s)** dans votre panier pour un montant de **{$total} €**.")
            ->line("Voici ce qui vous attend :");

        foreach (array_slice($this->cart->items, 0, 3) as $item) {
            $mail->line("- {$item['product_name']} × {$item['quantity']} — " . number_format($item['price'], 2, ',', ' ') . ' €');
        }

        if ($itemCount > 3) {
            $mail->line("...et " . ($itemCount - 3) . " autre(s) article(s).");
        }

        return $mail
            ->action('Reprendre mon panier', config('app.url') . '/cart')
            ->line('Votre panier est sauvegardé et vous attend !')
            ->salutation("À bientôt sur Monghetto");
    }

    // Envoi push FCM en plus de l'email
    public function sendPush(object $notifiable): void
    {
        $total = number_format((float) $this->cart->total, 2, ',', ' ');

        app(PushNotificationService::class)->sendToUser(
            $notifiable,
            'Votre panier vous attend 🛒',
            "Vous avez {$total} € d'articles qui vous attendent.",
            ['type' => 'abandoned_cart', 'total' => (string) $this->cart->total]
        );
    }
}
