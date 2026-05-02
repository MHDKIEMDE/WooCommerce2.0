<?php

use App\Http\Controllers\Web\AuthController as WebAuthController;
use App\Http\Controllers\Web\CartController as WebCartController;
use App\Http\Controllers\Web\ShopController;
use App\Http\Controllers\Web\Admin\SlideController;
use App\Http\Controllers\Web\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Web\Admin\PromotionController;
use App\Http\Controllers\Web\Admin\HomeSettingsController;
use App\Http\Controllers\Web\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Web\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Web\Admin\NotificationSettingsController;
use App\Http\Controllers\Web\Admin\SocialSettingsController;
use App\Http\Controllers\Web\Admin\ShopSettingsController;
use App\Http\Controllers\Web\Admin\ThemeSettingsController;
use App\Http\Controllers\Web\AccountController;
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\TestimonialController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\UserController as AdminUserController;
use App\Http\Controllers\Web\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Web\Admin\StockController as AdminStockController;
use App\Http\Controllers\Web\Admin\DeliveryZoneController;
use App\Http\Controllers\Web\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Web\Admin\ShopController as AdminShopController;
use App\Http\Controllers\Web\Admin\ShopTemplateController;
use App\Http\Controllers\Web\MarketplaceController;
use App\Http\Controllers\Web\SellerOnboardingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Boutique Blade
|--------------------------------------------------------------------------
*/

// ── Page d'accueil ────────────────────────────────────────────────────────
Route::get('/', [ShopController::class, 'home'])->name('home');

// ── Catalogue ─────────────────────────────────────────────────────────────
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.show');

// ── Catégories ────────────────────────────────────────────────────────────
Route::get('/category/{slug}', [ShopController::class, 'category'])->name('shop.category');

// ── Recherche ─────────────────────────────────────────────────────────────
Route::get('/search',         [ShopController::class, 'search'])->name('shop.search');
Route::get('/search/suggest', [ShopController::class, 'suggest'])->name('shop.suggest');

// ── Auth Web (invités seulement) ──────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [WebAuthController::class, 'login'])->name('login.post');
    Route::get('/register', [WebAuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[WebAuthController::class, 'register'])->name('register.post');
    Route::get('/forgot-password',  [WebAuthController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [WebAuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}',  [WebAuthController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [WebAuthController::class, 'resetPassword'])->name('password.update');
});

// ── Déconnexion (authentifié) ─────────────────────────────────────────────
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Panier (accessible invité + connecté) ────────────────────────────────
Route::get('/cart',              [WebCartController::class, 'index'])->name('cart.index');
Route::get('/cart/mini',         [WebCartController::class, 'mini'])->name('cart.mini');
Route::post('/cart/add',         [WebCartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{id}',       [WebCartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{id}',      [WebCartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/coupon',      [WebCartController::class, 'applyCoupon'])->name('cart.coupon');
Route::delete('/cart/coupon',    [WebCartController::class, 'removeCoupon'])->name('cart.coupon.remove');

// ── Marketplace public ────────────────────────────────────────────────────
Route::get('/boutiques',         [MarketplaceController::class, 'index'])->name('marketplace.index');
Route::get('/boutiques/{slug}',  [MarketplaceController::class, 'show'])->name('marketplace.show');

// ── Inscription vendeur ────────────────────────────────────────────────────
Route::get('/devenir-vendeur',   [SellerOnboardingController::class, 'show'])->name('seller.register')->middleware('auth');
Route::post('/devenir-vendeur',  [SellerOnboardingController::class, 'store'])->name('seller.register.store')->middleware('auth');
Route::get('/ma-boutique/attente', [SellerOnboardingController::class, 'pending'])->name('seller.pending')->middleware('auth');

// ── Pages statiques ───────────────────────────────────────────────────────
Route::view('/contact', 'contact')->name('contact');
Route::view('/about', 'about')->name('about');
Route::get('/checkout',  [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::post('/checkout/stripe/intent', [CheckoutController::class, 'createStripeIntent'])->name('checkout.stripe.intent')->middleware('auth');
Route::get('/checkout/confirmation/{orderNumber}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
Route::get('/testimonial', [TestimonialController::class, 'index'])->name('testimonial.index');

// ── Compte utilisateur ────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/account',                          [AccountController::class, 'profile'])->name('account.profile');
    Route::get('/account/orders',                   [AccountController::class, 'orders'])->name('account.orders');
    Route::get('/account/orders/{id}/invoice',      [AccountController::class, 'downloadInvoice'])->name('account.orders.invoice');
    Route::get('/account/wishlist',                 [AccountController::class, 'wishlist'])->name('account.wishlist');
    Route::post('/account/wishlist/toggle',         [AccountController::class, 'toggleWishlist'])->name('account.wishlist.toggle');
    Route::get('/account/edit',                     [AccountController::class, 'editProfile'])->name('account.edit');
    Route::put('/account/edit',                     [AccountController::class, 'updateProfile'])->name('account.update');
    Route::post('/account/password',                [AccountController::class, 'changePassword'])->name('account.password');
    Route::get('/account/addresses',                [AccountController::class, 'addresses'])->name('account.addresses');
    Route::post('/account/addresses',               [AccountController::class, 'storeAddress'])->name('account.addresses.store');
    Route::put('/account/addresses/{id}',           [AccountController::class, 'updateAddress'])->name('account.addresses.update');
    Route::delete('/account/addresses/{id}',        [AccountController::class, 'destroyAddress'])->name('account.addresses.destroy');
    Route::patch('/account/addresses/{id}/default', [AccountController::class, 'setDefaultAddress'])->name('account.addresses.default');
});

// ── Dashboard Admin — Carrousel Slides ───────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('dashboard')->name('admin.')->group(function () {
    Route::get('/',             [DashboardController::class, 'index'])->name('dashboard');
    Route::get('users',                   [AdminUserController::class, 'index'])->name('users.index');
    Route::get('users/{user}/edit',       [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}',            [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}',         [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::get('charts.html',             fn () => redirect()->route('admin.users.index'))->name('charts.redirect');
    Route::patch('users/{user}/toggle',   [AdminUserController::class, 'toggleStatus'])->name('users.toggle');
    Route::get('orders',                  [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}',          [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::patch('orders/{order}/payment',[AdminOrderController::class, 'updatePayment'])->name('orders.payment');
    Route::get('orders/{order}/invoice', [AdminOrderController::class, 'downloadInvoice'])->name('orders.invoice');
    Route::get('stock',                   [AdminStockController::class, 'index'])->name('stock.index');
    Route::patch('stock/{product}',       [AdminStockController::class, 'update'])->name('stock.update');
    Route::resource('slides', SlideController::class);
    Route::resource('testimonials', AdminTestimonialController::class)->except(['show']);
    Route::resource('promotions', PromotionController::class)->except(['show']);
    Route::get('home-settings', [HomeSettingsController::class, 'edit'])->name('home-settings.edit');
    Route::put('home-settings', [HomeSettingsController::class, 'update'])->name('home-settings.update');
    Route::resource('products', AdminProductController::class)->except(['show']);
    Route::resource('categories', AdminCategoryController::class)->except(['show']);
    Route::get('notification-settings',  [NotificationSettingsController::class, 'edit'])->name('notification-settings.edit');
    Route::put('notification-settings',  [NotificationSettingsController::class, 'update'])->name('notification-settings.update');
    Route::post('notification-settings/test', [NotificationSettingsController::class, 'test'])->name('notification-settings.test');
    Route::get('social-settings', [SocialSettingsController::class, 'edit'])->name('social-settings.edit');
    Route::put('social-settings', [SocialSettingsController::class, 'update'])->name('social-settings.update');
    Route::get('shop-settings', [ShopSettingsController::class, 'edit'])->name('shop-settings.edit');
    Route::put('shop-settings', [ShopSettingsController::class, 'update'])->name('shop-settings.update');
    Route::get('theme-settings', [ThemeSettingsController::class, 'edit'])->name('theme-settings.edit');
    Route::put('theme-settings', [ThemeSettingsController::class, 'update'])->name('theme-settings.update');
    Route::resource('delivery-zones', DeliveryZoneController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('templates', ShopTemplateController::class)->except(['show']);
    Route::post('templates/{template}/palettes',              [ShopTemplateController::class, 'storePalette'])->name('templates.palettes.store');
    Route::delete('templates/{template}/palettes/{palette}',  [ShopTemplateController::class, 'destroyPalette'])->name('templates.palettes.destroy');
    Route::get('shops',                    [AdminShopController::class, 'index'])->name('shops.index');
    Route::patch('shops/{shop}/approve',   [AdminShopController::class, 'approve'])->name('shops.approve');
    Route::patch('shops/{shop}/suspend',   [AdminShopController::class, 'suspend'])->name('shops.suspend');
    Route::get('notifications',             [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/send',        [AdminNotificationController::class, 'send'])->name('notifications.send');
    Route::post('notifications/broadcast',  [AdminNotificationController::class, 'broadcast'])->name('notifications.broadcast');
    Route::delete('notifications/{id}',     [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');
});
