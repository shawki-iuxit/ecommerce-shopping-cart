<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Products/Index');
})->name('home');

Route::get('/products', function () {
    return Inertia::render('Products/Index');
})->name('products.index');

Route::get('/cart', function () {
    return Inertia::render('Cart/Index');
})->middleware(['auth', 'verified'])->name('cart.index');

Route::get('/checkout', function () {
    return Inertia::render('Checkout/Index');
})->middleware(['auth', 'verified'])->name('checkout.index');

Route::get('/orders', function () {
    return Inertia::render('Orders/Index');
})->middleware(['auth', 'verified'])->name('orders.index');

Route::get('/orders/{id}', function ($id) {
    return Inertia::render('Orders/Show', ['orderId' => $id]);
})->middleware(['auth', 'verified'])->name('orders.show');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
