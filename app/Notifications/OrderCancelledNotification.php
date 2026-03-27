<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Commande {$this->order->order_number} annulée")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Votre commande **{$this->order->order_number}** a été annulée.")
            ->line("Si vous n'êtes pas à l'origine de cette annulation, contactez le support.")
            ->action('Voir ma commande', url('/account/orders/' . $this->order->id))
            ->line('Nous espérons vous revoir bientôt.');
    }

    public function fcmPayload(): array
    {
        return [
            'title' => 'Commande annulée',
            'body'  => "Votre commande {$this->order->order_number} a été annulée.",
            'data'  => ['type' => 'order_cancelled', 'order_id' => $this->order->id],
        ];
    }
}
