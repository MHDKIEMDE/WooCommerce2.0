<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderShippedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Commande {$this->order->order_number} expédiée")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Votre commande **{$this->order->order_number}** est en route !");

        if ($this->order->tracking_number) {
            $mail->line("Numéro de suivi : **{$this->order->tracking_number}**");
        }

        return $mail
            ->action('Suivre ma commande', url('/account/orders/' . $this->order->id))
            ->line('Merci pour votre confiance !');
    }

    public function fcmPayload(): array
    {
        return [
            'title' => 'Commande expédiée !',
            'body'  => "Votre commande {$this->order->order_number} est en route.",
            'data'  => [
                'type'     => 'order_shipped',
                'order_id' => $this->order->id,
            ],
        ];
    }
}
