<?php

namespace App\Notifications;

use App\Models\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShopSuspendedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Shop $shop,
        private readonly ?string $reason = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Votre boutique « {$this->shop->name} » a été suspendue")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Nous vous informons que votre boutique **{$this->shop->name}** a été temporairement suspendue.");

        if ($this->reason) {
            $mail->line("Motif : {$this->reason}");
        }

        return $mail
            ->line("Si vous pensez qu'il s'agit d'une erreur, contactez notre support.")
            ->action('Contacter le support', config('app.url') . '/support')
            ->line("L'équipe Monghetto");
    }
}
