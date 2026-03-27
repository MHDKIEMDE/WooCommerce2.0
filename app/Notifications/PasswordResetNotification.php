<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private string $otp) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Code de réinitialisation de mot de passe')
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Votre code de réinitialisation est :")
            ->line("## **{$this->otp}**")
            ->line("Ce code est valable **15 minutes**.")
            ->line("Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.");
    }
}
