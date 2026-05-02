<?php

namespace App\Notifications;

use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisputeResolvedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Dispute $dispute) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order = $this->dispute->order;

        $mail = (new MailMessage)
            ->subject("Litige résolu — Commande #{$order->order_number}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Le litige concernant la commande **#{$order->order_number}** a été résolu.");

        if ($this->dispute->resolution_note) {
            $mail->line("**Décision :** {$this->dispute->resolution_note}");
        }

        if ($this->dispute->refund_issued) {
            $mail->line("✅ Un remboursement a été émis sur votre moyen de paiement.");
        }

        return $mail
            ->action('Voir ma commande', config('app.url') . "/api/v1/orders/{$order->id}")
            ->line("Merci de votre confiance. L'équipe Monghetto");
    }
}
