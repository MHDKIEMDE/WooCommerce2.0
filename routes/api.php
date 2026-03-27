<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — /api/v1/
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ── Auth (public) ─────────────────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('register',          [\App\Http\Controllers\Api\V1\Auth\AuthController::class, 'register']);
        Route::post('login',             [\App\Http\Controllers\Api\V1\Auth\AuthController::class, 'login']);
        Route::post('forgot-password',   [\App\Http\Controllers\Api\V1\Auth\AuthController::class, 'forgotPassword']);
        Route::post('verify-reset-code', [\App\Http\Controllers\Api\V1\Auth\AuthController::class, 'verifyResetCode']);
        Route::post('reset-password',    [\App\Http\Controllers\Api\V1\Auth\AuthController::class, 'resetPassword']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout',        [\App\Http\Controllers\Api\V1\Auth\AuthController::class, 'logout']);
            Route::post('logout-all',    [\App\Http\Controllers\Api\V1\Auth\AuthController::class, 'logoutAll']);
            Route::post('verify-email',  [\App\Http\Controllers\Api\V1\Auth\AuthController::class, 'verifyEmail']);
        });
    });

    // ── Catalogue (public) ────────────────────────────────────────────────
    Route::get('products',                         [\App\Http\Controllers\Api\V1\ProductController::class, 'index']);
    Route::get('products/{slug}',                  [\App\Http\Controllers\Api\V1\ProductController::class, 'show']);
    Route::get('categories',                       [\App\Http\Controllers\Api\V1\CategoryController::class, 'index']);
    Route::get('categories/{slug}/products',       [\App\Http\Controllers\Api\V1\CategoryController::class, 'products']);
    Route::get('brands',                           [\App\Http\Controllers\Api\V1\BrandController::class, 'index']);
    Route::get('search',                           [\App\Http\Controllers\Api\V1\SearchController::class, 'index']);

    // ── Avis produits (public GET, authentifié POST) ──────────────────────
    Route::get('products/{product}/reviews',       [\App\Http\Controllers\Api\V1\ReviewController::class, 'index']);

    // ── Panier (mixte invité / connecté) ──────────────────────────────────
    Route::prefix('cart')->group(function () {
        Route::get('/',                            [\App\Http\Controllers\Api\V1\CartController::class, 'index']);
        Route::post('items',                       [\App\Http\Controllers\Api\V1\CartController::class, 'addItem']);
        Route::patch('items/{id}',                 [\App\Http\Controllers\Api\V1\CartController::class, 'updateItem']);
        Route::delete('items/{id}',                [\App\Http\Controllers\Api\V1\CartController::class, 'removeItem']);
        Route::post('coupon',                      [\App\Http\Controllers\Api\V1\CartController::class, 'applyCoupon']);
        Route::delete('coupon',                    [\App\Http\Controllers\Api\V1\CartController::class, 'removeCoupon']);
        Route::delete('/',                         [\App\Http\Controllers\Api\V1\CartController::class, 'clear']);
    });

    // ── Webhook Stripe (sans auth — signature vérifée dans le controller) ─
    Route::post('checkout/webhook',                [\App\Http\Controllers\Api\V1\CheckoutController::class, 'webhook']);

    // ── Routes authentifiées ──────────────────────────────────────────────
    Route::middleware(['auth:sanctum', 'active'])->group(function () {

        // Avis — POST seulement (achat vérifié)
        Route::post('products/{product}/reviews',  [\App\Http\Controllers\Api\V1\ReviewController::class, 'store']);

        // Checkout & commandes
        Route::get('checkout',                     [\App\Http\Controllers\Api\V1\CheckoutController::class, 'index']);
        Route::post('checkout',                    [\App\Http\Controllers\Api\V1\CheckoutController::class, 'store']);
        Route::get('orders',                       [\App\Http\Controllers\Api\V1\OrderController::class, 'index']);
        Route::get('orders/{id}',                  [\App\Http\Controllers\Api\V1\OrderController::class, 'show']);
        Route::get('orders/{id}/invoice',          [\App\Http\Controllers\Api\V1\OrderController::class, 'invoice']);

        // Compte client
        Route::prefix('account')->group(function () {
            Route::get('/',                        [\App\Http\Controllers\Api\V1\AccountController::class, 'show']);
            Route::patch('/',                      [\App\Http\Controllers\Api\V1\AccountController::class, 'update']);
            Route::post('avatar',                  [\App\Http\Controllers\Api\V1\AccountController::class, 'avatar']);
            Route::get('addresses',                [\App\Http\Controllers\Api\V1\AddressController::class, 'index']);
            Route::post('addresses',               [\App\Http\Controllers\Api\V1\AddressController::class, 'store']);
            Route::patch('addresses/{id}',         [\App\Http\Controllers\Api\V1\AddressController::class, 'update']);
            Route::delete('addresses/{id}',        [\App\Http\Controllers\Api\V1\AddressController::class, 'destroy']);
            Route::patch('addresses/{id}/default', [\App\Http\Controllers\Api\V1\AddressController::class, 'setDefault']);
        });

        // Wishlist
        Route::get('wishlist',                     [\App\Http\Controllers\Api\V1\WishlistController::class, 'index']);
        Route::post('wishlist',                    [\App\Http\Controllers\Api\V1\WishlistController::class, 'store']);
        Route::delete('wishlist/{product}',        [\App\Http\Controllers\Api\V1\WishlistController::class, 'destroy']);

        // Notifications
        Route::get('notifications',                [\App\Http\Controllers\Api\V1\NotificationController::class, 'index']);
        Route::patch('notifications/{id}/read',    [\App\Http\Controllers\Api\V1\NotificationController::class, 'markRead']);
        Route::post('notifications/read-all',      [\App\Http\Controllers\Api\V1\NotificationController::class, 'markAllRead']);
    });

    // ── Administration (authentifié + role:admin) ─────────────────────────
    Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {

        Route::get('dashboard',                    [\App\Http\Controllers\Api\V1\Admin\DashboardController::class, 'index']);

        // Produits
        Route::get('products/export',              [\App\Http\Controllers\Api\V1\Admin\ProductController::class, 'export']);
        Route::apiResource('products',             \App\Http\Controllers\Api\V1\Admin\ProductController::class);
        Route::patch('products/{id}/stock',        [\App\Http\Controllers\Api\V1\Admin\ProductController::class, 'updateStock']);
        Route::patch('products/{id}/status',       [\App\Http\Controllers\Api\V1\Admin\ProductController::class, 'updateStatus']);

        // Catégories
        Route::apiResource('categories',           \App\Http\Controllers\Api\V1\Admin\CategoryController::class);

        // Commandes
        Route::get('orders/export',                [\App\Http\Controllers\Api\V1\Admin\OrderController::class, 'export']);
        Route::get('orders',                       [\App\Http\Controllers\Api\V1\Admin\OrderController::class, 'index']);
        Route::get('orders/{id}',                  [\App\Http\Controllers\Api\V1\Admin\OrderController::class, 'show']);
        Route::patch('orders/{id}/status',         [\App\Http\Controllers\Api\V1\Admin\OrderController::class, 'updateStatus']);

        // Clients
        Route::get('users',                        [\App\Http\Controllers\Api\V1\Admin\UserController::class, 'index']);
        Route::get('users/{id}',                   [\App\Http\Controllers\Api\V1\Admin\UserController::class, 'show']);
        Route::patch('users/{id}/toggle-active',   [\App\Http\Controllers\Api\V1\Admin\UserController::class, 'toggleActive']);

        // Coupons
        Route::apiResource('coupons',              \App\Http\Controllers\Api\V1\Admin\CouponController::class);

        // Avis
        Route::get('reviews',                      [\App\Http\Controllers\Api\V1\Admin\ReviewController::class, 'index']);
        Route::patch('reviews/{id}/approve',       [\App\Http\Controllers\Api\V1\Admin\ReviewController::class, 'approve']);
        Route::delete('reviews/{id}',              [\App\Http\Controllers\Api\V1\Admin\ReviewController::class, 'destroy']);

        // Paramètres
        Route::get('settings',                     [\App\Http\Controllers\Api\V1\Admin\SettingController::class, 'index']);
        Route::patch('settings',                   [\App\Http\Controllers\Api\V1\Admin\SettingController::class, 'update']);

        // Rapports
        Route::get('reports/sales',                [\App\Http\Controllers\Api\V1\Admin\ReportController::class, 'sales']);
        Route::get('reports/products',             [\App\Http\Controllers\Api\V1\Admin\ReportController::class, 'products']);
        Route::get('reports/customers',            [\App\Http\Controllers\Api\V1\Admin\ReportController::class, 'customers']);
        Route::get('reports/stock',                [\App\Http\Controllers\Api\V1\Admin\ReportController::class, 'stock']);
    });
});
