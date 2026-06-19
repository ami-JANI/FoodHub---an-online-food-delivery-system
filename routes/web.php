<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AdminApprovalController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\RestaurantAuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MenuItemDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\RestaurantDashboardController;
use App\Http\Controllers\RestaurantProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RestaurantController::class, 'index'])->name('home');
Route::get('/restaurants/{slug}', [RestaurantController::class, 'show'])->name('restaurants.show');

Route::post('/location', [LocationController::class, 'update'])->name('location.update');
Route::delete('/location', [LocationController::class, 'clear'])->name('location.clear');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/{menuItem}/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{menuItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{menuItem}', [CartController::class, 'remove'])->name('cart.remove');

// Customer auth
Route::middleware('guest:web')->group(function () {
    Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [CustomerAuthController::class, 'login']);
    Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [CustomerAuthController::class, 'register']);
});

Route::middleware('auth:web')->group(function () {
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');

    Route::get('/account', [ProfileController::class, 'show'])->name('account.show');
    Route::get('/account/addresses/create', [AddressController::class, 'create'])->name('addresses.create');
    Route::post('/account/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::get('/account/addresses/{address}/edit', [AddressController::class, 'edit'])->name('addresses.edit');
    Route::put('/account/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/account/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::patch('/account/addresses/{address}/default', [AddressController::class, 'setDefault'])->name('addresses.default');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/order/{order}/confirmation', [CheckoutController::class, 'success'])->name('cart.success');
});

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

        Route::get('/menu-items/create', [MenuItemDashboardController::class, 'create'])->name('menu-items.create');
        Route::post('/menu-items', [MenuItemDashboardController::class, 'store'])->name('menu-items.store');
        Route::get('/menu-items/{menuItem}/edit', [MenuItemDashboardController::class, 'edit'])->name('menu-items.edit');
        Route::put('/menu-items/{menuItem}', [MenuItemDashboardController::class, 'update'])->name('menu-items.update');

        Route::get('/profile/edit', [RestaurantProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [RestaurantProfileController::class, 'update'])->name('profile.update');
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

        Route::patch('/restaurants/{restaurant}/approve', [AdminApprovalController::class, 'approveRestaurant'])->name('restaurants.approve');
        Route::delete('/restaurants/{restaurant}/reject', [AdminApprovalController::class, 'rejectRestaurant'])->name('restaurants.reject');

        Route::patch('/menu-items/{menuItem}/approve', [AdminApprovalController::class, 'approveMenuItem'])->name('menu-items.approve');
        Route::delete('/menu-items/{menuItem}/reject', [AdminApprovalController::class, 'rejectMenuItem'])->name('menu-items.reject');

        Route::patch('/profile-updates/{updateRequest}/approve', [AdminApprovalController::class, 'approveProfileUpdate'])->name('profile-updates.approve');
        Route::patch('/profile-updates/{updateRequest}/reject', [AdminApprovalController::class, 'rejectProfileUpdate'])->name('profile-updates.reject');
    });
});
