<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\RestaurantAuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\RestaurantDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RestaurantController::class, 'index'])->name('home');
Route::get('/restaurants/{slug}', [RestaurantController::class, 'show'])->name('restaurants.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/{menuItem}/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{menuItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{menuItem}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

// Customer auth
Route::middleware('guest:web')->group(function () {
    Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [CustomerAuthController::class, 'login']);
    Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [CustomerAuthController::class, 'register']);
});
Route::post('/logout', [CustomerAuthController::class, 'logout'])->middleware('auth:web')->name('logout');

// Restaurant (merchant) auth
Route::prefix('restaurant')->name('restaurant.')->group(function () {
    Route::middleware('guest:restaurant')->group(function () {
        Route::get('/login', [RestaurantAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [RestaurantAuthController::class, 'login']);
        Route::get('/register', [RestaurantAuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [RestaurantAuthController::class, 'register']);
    });

    Route::middleware('auth:restaurant')->group(function () {
        Route::post('/logout', [RestaurantAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [RestaurantDashboardController::class, 'index'])->name('dashboard');
    });
});

// Admin auth (no public registration — accounts are created manually)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login']);
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    });
});
