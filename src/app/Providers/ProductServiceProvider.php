<?php

namespace VCComponent\Laravel\Product\Providers;

use Illuminate\Support\ServiceProvider;
use VCComponent\Laravel\Product\Contracts\ViewProductDetailControllerInterface;
use VCComponent\Laravel\Product\Contracts\ViewProductListControllerInterface;
use VCComponent\Laravel\Product\Http\Controllers\Web\ProductDetailController as ViewProductDetailController;
use VCComponent\Laravel\Product\Http\Controllers\Web\ProductListController as ViewProductListController;
use VCComponent\Laravel\Product\Products\Product;
use VCComponent\Laravel\Product\Repositories\ProductRepository;
use VCComponent\Laravel\Product\Repositories\ProductRepositoryEloquent;

class ProductServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->publishes([
            __DIR__ . '/../../config/product.php' => config_path('product.php'),
        ], 'config');

        $this->loadViewsFrom(__DIR__ . '/../../resources/views/', 'product-manager');

    }

    /**
     * Register any package services
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ProductRepository::class, ProductRepositoryEloquent::class);
        $this->registerControllers();

        $this->app->singleton('moduleProduct.product', function () {
            return new Product();
        });

        $this->app->bind(ContractsProduct::class, 'moduleProduct.product');
    }

    private function registerControllers()
    {
        $this->app->bind(ViewProductListControllerInterface::class, ViewProductListController::class);
        $this->app->bind(ViewProductDetailControllerInterface::class, ViewProductDetailController::class);
    }

    public function provides()
    {
        return [
            ContractsProduct::class,
            'moduleProduct.product',
        ];
    }

}
