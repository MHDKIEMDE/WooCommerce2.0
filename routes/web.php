<?php

use App\Http\Controllers\Web\AuthController as WebAuthController;
use App\Http\Controllers\Web\CartController as WebCartController;
use App\Http\Controllers\Web\ShopController;
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
Route::view('/checkout', 'checkout')->name('home.checkout');
Route::view('/testimonial', 'testimonial')->name('testimonial.index');

// ── Profil utilisateur ────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::view('/profile', 'profile')->name('user.profile');
});
