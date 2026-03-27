<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class StockAlertCommand extends Command
{
    protected $signature = 'app:stock-alert';

    protected $description = 'Envoie une alerte email aux admins pour les produits en rupture ou en stock critique.';

    public function handle(): int
    {
        $threshold = (int) config('shop.low_stock_threshold', 5);

        $lowStock = Product::where('status', 'active')
            ->where('stock_quantity', '<=', $threshold)
            ->orderBy('stock_quantity')
            ->get(['id', 'name', 'sku', 'stock_quantity', 'low_stock_threshold']);

        if ($lowStock->isEmpty()) {
            $this->info('Aucun produit en stock critique.');
            return self::SUCCESS;
        }

        $this->info("Produits en stock critique : {$lowStock->count()}");

        $admins = User::where('role', 'admin')->pluck('email');

        if ($admins->isEmpty()) {
            $this->warn('Aucun administrateur trouvé pour envoyer l\'alerte.');
            return self::SUCCESS;
        }

        $lines = $lowStock->map(fn ($p) =>
            "• [{$p->sku}] {$p->name} — stock: {$p->stock_quantity} unité(s)"
        )->join("\n");

        foreach ($admins as $email) {
            Mail::raw(
                "Bonjour,\n\nLes produits suivants sont en stock critique :\n\n{$lines}\n\nVeuillez réapprovisionner au plus vite.\n\nAgri-Shop",
                function ($message) use ($email, $lowStock) {
                    $message->to($email)
                        ->subject("[Agri-Shop] Alerte stock — {$lowStock->count()} produit(s) critique(s)");
                }
            );
        }

        $this->info("Alerte envoyée à " . $admins->count() . " administrateur(s).");

        return self::SUCCESS;
    }
}
