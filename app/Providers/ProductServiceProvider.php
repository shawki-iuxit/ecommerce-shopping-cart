<?php

namespace App\Providers;

use App\Domain\Cart\Repositories\CartRepositoryInterface;
use App\Domain\Cart\Repositories\EloquentCartRepository;
use App\Domain\Product\Repositories\EloquentProductRepository;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class ProductServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            ProductRepositoryInterface::class,
            EloquentProductRepository::class
        );

        $this->app->bind(
            CartRepositoryInterface::class,
            EloquentCartRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
