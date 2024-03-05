<?php

use App\Http\Controllers\adminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// Routes pour les user 
Route::get('/', [UserController::class, 'welcome'])->name('home');
Route::get('/404', [UserController::class, 'erreur404'])->name('home.404');
Route::get('/cart', [ProductController::class, 'cart'])->name('home.cart');
Route::get('/contact', [UserController::class, 'contact'])->name('home.contact');
Route::get('/boutique', [ProductController::class, 'boutique'])->name('home.boutique');
Route::get('/checkout', [ProductController::class, 'checkout'])->name('home.checkout');
Route::get('/testimonial', [UserController::class, 'testimonial'])->name('home.testimonial');
Route::get('/product/{id}', [UserController::class, 'showProduct'])->name('produits.show');
// Route::get('/product/{id}', [UserController::class, 'showProduct'])->name('produits.show');

// Router des user
Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
Route::get('/editprofile', [UserController::class, 'editProfile'])->name('user.editProfile');
Route::put('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');

// Routes pour les produits admin
Route::get('/dashboard', [adminController::class, 'home'])->name('admin.home');
Route::get('/dashboard/product', [adminController::class, 'indexProduct'])->name('produits.index');
Route::get('/dashboard/products/create', [adminController::class, 'createProduct'])->name('produits.create');
Route::post('/dashboard/product/store', [adminController::class, 'storeProduct'])->name('produits.store');
// Route::get('/dashboard/product/{id}', [adminController::class, 'showProduct'])->name('produits.show');
Route::get('/dashboard/product/{id}/edit', [adminController::class, 'editProduct'])->name('produits.edit');
Route::put('/dashboard/product/{id}', [adminController::class, 'updateProduct'])->name('produits.update');
Route::delete('/dashboard/product/{id}', [adminController::class, 'destroyProduct'])->name('produits.destroy');

// Routes pour les catégories admin
Route::get('/dashboard/categories', [adminController::class, 'indexCategorie'])->name('categories.index');
Route::get('/dashboard/categories/create', [adminController::class, 'createCategorie'])->name('categories.create');
Route::post('/dashboard/categories', [adminController::class, 'storeCategorie'])->name('categories.store');
Route::get('/dashboard/categories/{id}', [adminController::class, 'showCategorie'])->name('categories.show');
Route::get('/dashboard/categories/{id}/edit', [adminController::class, 'editCategorie'])->name('categories.edit');
Route::put('/dashboard/categories{id}', [adminController::class, 'updateCategorie'])->name('categories.update');
Route::delete('/dashboard/categories{id}', [adminController::class, 'destroyCategorie'])->name('categories.destroy');


// //                                           testimonial 
Route::get('/testimonial', [TestimonialController::class, 'indexTestimonial'])->name('testimonial.index');
Route::get('/testimonial/create', [TestimonialController::class, 'createTestimonial'])->name('testimonial.create');
Route::post('/testimonial/store', [TestimonialController::class, 'storeTestimonial'])->name('testimonial.create');
Route::get('/testimonial/{id}', [TestimonialController::class, 'showTestimonial'])->name('');
Route::get('/testimonial/{id}/edit', [TestimonialController::class, 'editTestimonial'])->name('');
Route::put('/testimonial{id}', [TestimonialController::class, 'updateTestimonial'])->name('');
Route::delete('/testimonial{id}', [TestimonialController::class, 'destroyTestimonial'])->name('');


// admin
Route::get('dashboard/testimonial', [adminController::class, 'indexTestimonial'])->name('testimonial.dashboard');
Route::get('dashboard/comment', [adminController::class, 'indexComment'])->name('comment.dashboard');




Route::get('/recherche', [SearchController::class, 'search'])->name('search');
