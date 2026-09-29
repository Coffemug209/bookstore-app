<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (User-facing storefront)
|--------------------------------------------------------------------------
*/

// Home/Landing page
Route::livewire('/', 'pages::home')->name('home');

// About Us page
Route::livewire('/about', 'pages::about')->name('about');

// Contact page
Route::livewire('/contact', 'pages::contact')->name('contact');

// Book Catalog (public)
Route::livewire('/catalog', 'pages::catalog')->name('catalog');

// Cart & Checkout (require auth)
Route::middleware('auth')->group(function () {
    Route::livewire('/cart', 'pages::cart')->name('cart');
    Route::livewire('/checkout', 'pages::checkout')->name('checkout');
});

/*
|--------------------------------------------------------------------------
| Admin / Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Admin-only routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::livewire('/books', 'admin.book-management')->name('books');
        Route::livewire('/categories', 'admin.category-management')->name('categories');
        Route::livewire('/users', 'admin.user-list')->name('users');
        Route::livewire('/orders', 'admin.order-list')->name('orders');
    });
});

require __DIR__.'/settings.php';
