<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(['timeout' => 10]);
    }

    private function phone(): string
    {
        return Setting::get('whatsapp_phone', config('services.whatsapp.phone', ''));
    }

    private function apiKey(): string
    {
        return Setting::get('whatsapp_apikey', config('services.whatsapp.callmebot_apikey', ''));
    }

    private function enabled(): bool
    {
        return Setting::get('whatsapp_enabled', '0') === '1';
    }

    /**
     * Envoie une notification WhatsApp au propriétaire de la boutique
     * pour chaque nouvelle commande.
     */
    public function notifyNewOrder(Order $order): void
    {
        if (! $this->enabled()) {
            return;
        }

        if (empty($this->phone()) || empty($this->apiKey())) {
            Log::warning('WhatsApp: numéro ou clé API non configuré dans le dashboard.');
            return;
        }

        $this->send($this->buildOrderMessage($order));
    }

    /**
     * Envoie un message texte libre via CallMeBot.
     */
    public function send(string $message): bool
    {
        $phone  = $this->phone();
        $apiKey = $this->apiKey();

        if (empty($phone) || empty($apiKey)) {
            Log::warning('WhatsApp: numéro ou clé API manquant.');
            return false;
        }

        try {
            $this->client->get('https://api.callmebot.com/whatsapp.php', [
                'query' => [
                    'phone'  => $phone,
                    'apikey' => $apiKey,
                    'text'   => $message,
                ],
            ]);

            return true;
        } catch (GuzzleException $e) {
            Log::error('WhatsApp CallMeBot error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Génère le message de notification pour une commande.
     */
    private function buildOrderMessage(Order $order): string
    {
        $order->loadMissing('items');

        $addr   = $order->billing_address;
        $client = ($addr['first_name'] ?? '') . ' ' . ($addr['last_name'] ?? '');
        $phone  = $addr['phone'] ?? 'N/A';
        $city   = $addr['city'] ?? '';
        $address= $addr['address'] ?? '';

        $paymentLabel = match ($order->payment_method) {
            'cash_on_delivery' => 'Paiement à la livraison',
            'bank_transfer'    => 'Virement bancaire',
            default            => $order->payment_method,
        };

        $lines = [];
        foreach ($order->items as $item) {
            $lines[] = "• {$item->product_name} x{$item->quantity} — "
                . number_format($item->total_price, 0, ',', ' ') . ' FCFA';
        }
        $itemsList = implode("\n", $lines);

        $total = number_format($order->total, 0, ',', ' ');

        $clientPhone = $this->sanitizePhone($phone);

        return "🛒 *NOUVELLE COMMANDE — {$order->order_number}*\n\n"
            . "👤 *Client :* {$client}\n"
            . "📞 *Téléphone :* {$phone}\n"
            . "📍 *Adresse :* {$address}, {$city}\n"
            . "💳 *Paiement :* {$paymentLabel}\n\n"
            . "*Articles :*\n{$itemsList}\n\n"
            . "💰 *Total : {$total} FCFA*\n\n"
            . "⚡ Répondre : https://wa.me/{$clientPhone}";
    }

    /**
     * Supprime les caractères non numériques d'un numéro (pour le lien wa.me).
     */
    private function sanitizePhone(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }
}
