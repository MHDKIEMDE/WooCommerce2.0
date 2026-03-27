<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Bienvenue sur Agri-Shop !')
            ->greeting("Bienvenue {$notifiable->name} !")
            ->line("Votre compte a été créé avec succès.")
            ->line("Découvrez nos produits biologiques frais, livrés directement chez vous.")
            ->action('Découvrir la boutique', url('/shop'))
            ->line('À très bientôt !');
    }
}
