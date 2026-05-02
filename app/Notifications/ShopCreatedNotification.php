<?php

namespace App\Notifications;

use App\Models\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShopCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Shop $shop) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Votre boutique « {$this->shop->name} » est en cours de validation")
            ->greeting("Bonjour {$notifiable->name} !")
            ->line("Votre boutique **{$this->shop->name}** a bien été créée et est maintenant en attente de validation par notre équipe.")
            ->line("Nous examinons votre dossier sous 24 à 48h. Vous recevrez un email dès que votre boutique sera activée.")
            ->line("Récapitulatif :")
            ->line("- Nom : {$this->shop->name}")
            ->line("- Sous-domaine : {$this->shop->subdomain}.monghetto.com")
            ->line("- Template : {$this->shop->template?->name}")
            ->action('Accéder à mon espace vendeur', config('app.url') . '/api/v1/seller/shop')
            ->line('Merci de rejoindre Monghetto !');
    }
}
