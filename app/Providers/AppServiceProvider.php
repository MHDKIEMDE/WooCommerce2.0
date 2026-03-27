<?php

namespace App\Providers;

use App\Events\OrderCancelled;
use App\Events\OrderDelivered;
use App\Events\OrderPlaced;
use App\Events\OrderShipped;
use App\Events\PaymentConfirmed;
use App\Listeners\DecrementStockOnPayment;
use App\Listeners\SendOrderNotification;
use App\Listeners\SendPushNotification;
use App\Models\Product;
use App\Observers\ProductObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Product::observe(ProductObserver::class);

        // Stock decrement on successful payment
        Event::listen(PaymentConfirmed::class, DecrementStockOnPayment::class);

        // Email notifications for order lifecycle
        Event::listen(OrderPlaced::class,    SendOrderNotification::class);
        Event::listen(OrderShipped::class,   SendOrderNotification::class);
        Event::listen(OrderDelivered::class, SendOrderNotification::class);
        Event::listen(OrderCancelled::class, SendOrderNotification::class);

        // Push notifications for order lifecycle
        Event::listen(OrderPlaced::class,    SendPushNotification::class);
        Event::listen(OrderShipped::class,   SendPushNotification::class);
        Event::listen(OrderDelivered::class, SendPushNotification::class);
        Event::listen(OrderCancelled::class, SendPushNotification::class);

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
