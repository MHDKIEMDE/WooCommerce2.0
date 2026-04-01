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
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\TestimonialController;
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
Route::get('/search', [ShopController::class, 'search'])->name('shop.search');

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
Route::post('/cart/add',         [WebCartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{id}',       [WebCartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{id}',      [WebCartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/coupon',      [WebCartController::class, 'applyCoupon'])->name('cart.coupon');
Route::delete('/cart/coupon',    [WebCartController::class, 'removeCoupon'])->name('cart.coupon.remove');

// ── Pages statiques ───────────────────────────────────────────────────────
Route::view('/contact', 'contact')->name('contact');
Route::view('/about', 'about')->name('about');
Route::get('/checkout',  [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/confirmation/{orderNumber}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
Route::get('/testimonial', [TestimonialController::class, 'index'])->name('testimonial.index');

// ── Profil utilisateur ────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::view('/profile', 'profile')->name('user.profile');
});

// ── Dashboard Admin — Carrousel Slides ───────────────────────────────────
Route::middleware(['auth', 'role:admin,super-admin'])->prefix('dashboard')->name('admin.')->group(function () {
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
});
