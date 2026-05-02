<?php

namespace App\Console\Commands;

use App\Models\AbandonedCart;
use App\Models\CartItem;
use App\Models\User;
use Illuminate\Console\Command;

class TrackAbandonedCartsCommand extends Command
{
    protected $signature   = 'carts:track-abandoned';
    protected $description = 'Enregistre les paniers abandonnés (non commandés depuis 24h)';

    public function handle(): int
    {
        // Utilisateurs avec des articles dans le panier mais aucune commande dans les 24h
        $usersWithCart = CartItem::whereNotNull('user_id')
            ->where('updated_at', '<=', now()->subHours(24))
            ->where('updated_at', '>=', now()->subDays(7)) // ne pas tracker les très vieux paniers
            ->with(['product.primaryImage', 'product.shop:id,name,slug'])
            ->get()
            ->groupBy('user_id');

        foreach ($usersWithCart as $userId => $items) {
            $user = User::find($userId);
            if (! $user) continue;

            $total = round($items->sum(function ($item) {
                return ((float) ($item->product->price ?? 0)) * $item->quantity;
            }), 2);

            $snapshot = $items->map(fn ($item) => [
                'product_id'   => $item->product_id,
                'product_name' => $item->product->name ?? '',
                'quantity'     => $item->quantity,
                'price'        => $item->product->price ?? 0,
                'image'        => $item->product?->primaryImage?->url,
                'shop'         => $item->product?->shop?->name,
            ])->values()->toArray();

            AbandonedCart::updateOrCreate(
                ['user_id' => $userId],
                [
                    'items'            => $snapshot,
                    'total'            => $total,
                    'last_activity_at' => $items->max('updated_at'),
                    'notified_at'      => null, // réinitialiser si le panier a changé
                ]
            );
        }

        $this->info('Paniers abandonnés mis à jour : ' . $usersWithCart->count());

        return self::SUCCESS;
    }
}
