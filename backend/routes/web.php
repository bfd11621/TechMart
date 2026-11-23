<?php

use App\Http\Controllers\ShowroomController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ShowroomController::class, 'home'])->name('showroom.home');
Route::get('/catalog', [ShowroomController::class, 'catalog'])->name('showroom.catalog');
Route::get('/categories', [ShowroomController::class, 'categories'])->name('showroom.categories');
Route::get('/brands', [ShowroomController::class, 'brands'])->name('showroom.brands');
Route::get('/product/{slug}', [ShowroomController::class, 'product'])->name('showroom.product');
Route::get('/products/{slug}', fn (string $slug) => redirect("/product/{$slug}"));
Route::get('/about', [ShowroomController::class, 'about'])->name('showroom.about');
Route::get('/register', [ShowroomController::class, 'register'])->name('showroom.register');
Route::get('/services', [ShowroomController::class, 'services'])->name('showroom.services');
Route::get('/contact', [ShowroomController::class, 'contact'])->name('showroom.contact');
Route::get('/privacy', [ShowroomController::class, 'privacy'])->name('showroom.privacy');
Route::get('/terms', [ShowroomController::class, 'terms'])->name('showroom.terms');
Route::get('/cart', [ShowroomController::class, 'cart'])->name('showroom.cart');
Route::get('/compare', [ShowroomController::class, 'compare'])->name('showroom.compare');
