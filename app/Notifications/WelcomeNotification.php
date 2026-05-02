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
        if ($notifiable->isSeller()) {
            return (new MailMessage)
                ->subject('Bienvenue sur Monghetto — Créez votre boutique !')
                ->greeting("Bienvenue {$notifiable->name} !")
                ->line("Votre compte vendeur a été créé avec succès sur **Monghetto**.")
                ->line("Prochaines étapes :")
                ->line("1. Choisissez votre template parmi 7 niches disponibles")
                ->line("2. Sélectionnez une palette de couleurs harmonieuse")
                ->line("3. Ajoutez vos produits et commencez à vendre")
                ->action('Créer ma boutique', config('app.url') . '/api/v1/seller/shop')
                ->line('À très bientôt sur Monghetto !');
        }

        return (new MailMessage)
            ->subject('Bienvenue sur Monghetto !')
            ->greeting("Bienvenue {$notifiable->name} !")
            ->line("Votre compte a été créé avec succès sur **Monghetto**, la marketplace multi-vendeurs.")
            ->line("Découvrez des milliers de produits : mode, alimentaire, digital, artisanat et plus encore.")
            ->action('Découvrir la marketplace', config('app.url'))
            ->line('À très bientôt !');
    }
}
