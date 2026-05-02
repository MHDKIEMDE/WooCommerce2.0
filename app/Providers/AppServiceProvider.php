<?php

namespace App\Providers;

use App\Events\OrderCancelled;
use App\Events\OrderDelivered;
use App\Events\OrderPlaced;
use App\Events\OrderShipped;
use App\Events\PaymentConfirmed;
use App\Events\DisputeOpened;
use App\Events\DisputeResolved;
use App\Events\ShopApproved;
use App\Events\ShopCreated;
use App\Events\ShopSuspended;
use App\Listeners\DecrementStockOnPayment;
use App\Listeners\SendDisputeNotification;
use App\Listeners\SendOrderNotification;
use App\Listeners\SendPushNotification;
use App\Listeners\SendShopNotification;
use App\Models\Product;
use App\Models\Review;
use App\Observers\ProductObserver;
use App\Observers\ReviewObserver;
use App\Services\CartService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();

        Product::observe(ProductObserver::class);
        Review::observe(ReviewObserver::class);

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

        // Email notifications for dispute lifecycle
        Event::listen(DisputeOpened::class,   SendDisputeNotification::class);
        Event::listen(DisputeResolved::class,  SendDisputeNotification::class);

        // Email notifications for shop lifecycle
        Event::listen(ShopCreated::class,   SendShopNotification::class);
        Event::listen(ShopApproved::class,  SendShopNotification::class);
        Event::listen(ShopSuspended::class, SendShopNotification::class);

        // Partage du nombre d'articles dans le panier avec tous les layouts web
        View::composer('layouts.app', function ($view) {
            $cartService = app(CartService::class);
            $cart = $cartService->getCart(auth()->user());
            $view->with('cartCount', $cart['count']);
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
