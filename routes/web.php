<?php

use App\Http\Controllers\Web\AuthController as WebAuthController;
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

// ── Pages statiques ───────────────────────────────────────────────────────
Route::view('/contact', 'contact')->name('contact');
Route::view('/about', 'about')->name('about');
