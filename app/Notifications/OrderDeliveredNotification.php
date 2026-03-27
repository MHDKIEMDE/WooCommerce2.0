<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderDeliveredNotification extends Notification implements ShouldQueue
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
            ->subject("Commande {$this->order->order_number} livrée")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Votre commande **{$this->order->order_number}** a été livrée.")
            ->line("Nous espérons que vous êtes satisfait(e) de votre achat.")
            ->action('Laisser un avis', url('/account/orders/' . $this->order->id))
            ->line('À très bientôt !');
    }

    public function fcmPayload(): array
    {
        return [
            'title' => 'Commande livrée !',
            'body'  => "Votre commande {$this->order->order_number} a été livrée. Donnez votre avis !",
            'data'  => ['type' => 'order_delivered', 'order_id' => $this->order->id],
        ];
    }
}
