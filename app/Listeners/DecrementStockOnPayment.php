<?php

namespace App\Listeners;

use App\Events\PaymentConfirmed;
use App\Services\StockService;

class DecrementStockOnPayment
{
    public function __construct(private StockService $stockService) {}

    public function handle(PaymentConfirmed $event): void
    {
        foreach ($event->order->items as $item) {
            $this->stockService->decrement(
                $item->product_id,
                $item->quantity,
                $item->variant_id,
            );
        }
    }
}
