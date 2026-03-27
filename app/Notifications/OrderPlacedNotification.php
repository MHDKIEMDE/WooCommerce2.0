<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification implements ShouldQueue
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
            ->subject("Commande {$this->order->order_number} confirmée")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Votre commande **{$this->order->order_number}** a bien été reçue.")
            ->line("Montant total : **{$this->order->total} €**")
            ->action('Voir ma commande', url('/account/orders/' . $this->order->id))
            ->line('Merci pour votre achat !');
    }

    public function fcmPayload(): array
    {
        return [
            'title' => 'Commande confirmée !',
            'body'  => "Votre commande {$this->order->order_number} a été reçue.",
            'data'  => [
                'type'     => 'order_placed',
                'order_id' => $this->order->id,
            ],
        ];
    }
}
