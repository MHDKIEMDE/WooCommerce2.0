<?php

namespace App\Notifications;

use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDisputeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Dispute $dispute) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order  = $this->dispute->order;
        $buyer  = $this->dispute->user;

        return (new MailMessage)
            ->subject("Nouveau litige — Commande #{$order->order_number}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Un litige a été ouvert pour la commande **#{$order->order_number}**.")
            ->line("**Acheteur :** {$buyer->name} ({$buyer->email})")
            ->line("**Motif :** {$this->dispute->reason}")
            ->when($this->dispute->description, fn ($m) =>
                $m->line("**Description :** {$this->dispute->description}")
            )
            ->line("Veuillez répondre dans les plus brefs délais.")
            ->action('Voir le litige', config('app.url') . "/api/v1/disputes/{$this->dispute->id}")
            ->line("L'équipe Monghetto");
    }
}
