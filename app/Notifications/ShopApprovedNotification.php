<?php

namespace App\Notifications;

use App\Models\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShopApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Shop $shop) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $shopUrl = "https://{$this->shop->subdomain}.monghetto.com";

        return (new MailMessage)
            ->subject("🎉 Votre boutique « {$this->shop->name} » est maintenant en ligne !")
            ->greeting("Félicitations {$notifiable->name} !")
            ->line("Votre boutique **{$this->shop->name}** a été validée et est maintenant accessible au public.")
            ->line("Votre URL boutique : **{$shopUrl}**")
            ->line("Prochaines étapes :")
            ->line("1. Connectez votre compte Stripe pour recevoir les paiements")
            ->line("2. Ajoutez vos premiers produits")
            ->line("3. Personnalisez l'apparence de votre boutique")
            ->action('Gérer ma boutique', config('app.url') . '/api/v1/seller/dashboard')
            ->line('Bonne vente sur Monghetto !');
    }
}
