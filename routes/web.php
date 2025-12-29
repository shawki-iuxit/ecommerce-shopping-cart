<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Products/Index');
})->name('home');

Route::get('/products', function () {
    return Inertia::render('Products/Index');
})->name('products.index');

Route::get('/cart', function () {
    return Inertia::render('Cart/Index');
})->middleware(['auth', 'verified'])->name('cart.index');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
