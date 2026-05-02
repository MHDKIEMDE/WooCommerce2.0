<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $comments = [
            5 => [
                'Excellent produit, très frais à la livraison !',
                'Qualité irréprochable, je recommande vivement.',
                'Parfait, conforme à la description. Livraison rapide.',
                'Savoureux et bien emballé. Je reviendrai.',
            ],
            4 => [
                'Bon produit dans l\'ensemble, livraison légèrement en retard.',
                'Très bien, peut mieux faire sur l\'emballage.',
                'Satisfait du rapport qualité-prix.',
                'Produit frais, bon goût. Emballage simple mais efficace.',
            ],
            3 => [
                'Correct sans plus, je m\'attendais à mieux.',
                'Moyen. Quelques fruits légèrement abîmés.',
                'Passable, à améliorer pour la prochaine commande.',
            ],
            2 => [
                'Déçu, la qualité n\'était pas au rendez-vous.',
                'Livraison trop longue, produit un peu défraîchi.',
            ],
            1 => [
                'Très décevant, produit endommagé à l\'arrivée.',
            ],
        ];

        // Récupérer les commandes livrées avec leurs items
        $deliveredOrders = Order::where('status', 'delivered')
            ->with(['items.product', 'user'])
            ->get();

        foreach ($deliveredOrders as $order) {
            foreach ($order->items as $item) {
                if (! $item->product) {
                    continue;
                }

                // 70% de chance de laisser un avis
                if (rand(1, 10) > 7) {
                    continue;
                }

                // Éviter les doublons user+product
                $exists = Review::where('user_id', $order->user_id)
                    ->where('product_id', $item->product_id)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $rating  = $this->weightedRating();
                $pool    = $comments[$rating];
                $comment = $pool[array_rand($pool)];

                Review::create([
                    'product_id'  => $item->product_id,
                    'user_id'     => $order->user_id,
                    'order_id'    => $order->id,
                    'rating'      => $rating,
                    'comment'     => $comment,
                    'approved_at' => rand(0, 1) ? now()->subDays(rand(1, 20)) : null,
                ]);
            }
        }

        // Recalculer rating_avg sur tous les produits ayant des avis
        Product::whereHas('reviews', fn($q) => $q->whereNotNull('approved_at'))->each(function ($product) {
            $approved = $product->reviews()->whereNotNull('approved_at');
            $product->update([
                'rating_avg'   => round($approved->avg('rating'), 1),
                'rating_count' => $approved->count(),
            ]);
        });
    }

    private function weightedRating(): int
    {
        // Distribution réaliste : majorité 4-5 étoiles
        $rand = rand(1, 100);
        return match (true) {
            $rand <= 45 => 5,
            $rand <= 75 => 4,
            $rand <= 88 => 3,
            $rand <= 95 => 2,
            default     => 1,
        };
    }
}
