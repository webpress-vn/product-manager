<?php

namespace VCComponent\Laravel\Product\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use VCComponent\Laravel\Product\Contracts\ViewProductDetailControllerInterface;
use VCComponent\Laravel\Product\Contracts\ViewProductListControllerInterface;
use VCComponent\Laravel\Product\Entities\Attribute;
use VCComponent\Laravel\Product\Entities\Product as BaseModel;
use VCComponent\Laravel\Product\Entities\Schema;
use VCComponent\Laravel\Product\Entities\Variant;
use VCComponent\Laravel\Product\Http\Controllers\Web\ProductDetailController as ViewProductDetailController;
use VCComponent\Laravel\Product\Http\Controllers\Web\ProductListController as ViewProductListController;
use VCComponent\Laravel\Product\Products\Product;
use VCComponent\Laravel\Product\Repositories\AttributeRepository;
use VCComponent\Laravel\Product\Repositories\AttributeRepositoryEloquent;
use VCComponent\Laravel\Product\Repositories\AttributeValueRepository;
use VCComponent\Laravel\Product\Repositories\AttributeValueRepositoryEloquent;
use VCComponent\Laravel\Product\Repositories\ProductAttributeRepository;
use VCComponent\Laravel\Product\Repositories\ProductAttributeRepositoryEloquent;
use VCComponent\Laravel\Product\Repositories\ProductRepository;
use VCComponent\Laravel\Product\Repositories\ProductRepositoryEloquent;
use VCComponent\Laravel\Product\Repositories\ProductSchemaRepository;
use VCComponent\Laravel\Product\Repositories\ProductSchemaRepositoryEloquent;
use VCComponent\Laravel\Product\Repositories\ProductSchemaRuleRepository;
use VCComponent\Laravel\Product\Repositories\ProductSchemaRuleRepositoryEloquent;
use VCComponent\Laravel\Product\Repositories\ProductSchemaTypeRepository;
use VCComponent\Laravel\Product\Repositories\ProductSchemaTypeRepositoryEloquent;
use VCComponent\Laravel\Product\Repositories\VariantRepository;
use VCComponent\Laravel\Product\Repositories\VariantRepositoryEloquent;
use VCComponent\Laravel\Product\Services\SchemaService;

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
            __DIR__ . '/../../config/product.php'                                => config_path('product.php'),
            __DIR__ . '/../../resources/scss/productAttributes/_attributes.scss' => base_path('/resources/sass/productAttributes/_attributes.scss'),
        ], 'config');

        $this->loadViewsFrom(__DIR__ . '/../../resources/views/', 'product-manager');

        $this->bootEntityName();
    }

    /**
     * Register any package services
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ProductRepository::class, ProductRepositoryEloquent::class);
        $this->app->bind(AttributeRepository::class, AttributeRepositoryEloquent::class);
        $this->app->bind(AttributeValueRepository::class, AttributeValueRepositoryEloquent::class);
        $this->app->bind(ProductAttributeRepository::class, ProductAttributeRepositoryEloquent::class);
        $this->app->bind(VariantRepository::class, VariantRepositoryEloquent::class);
        $this->app->bind(ProductSchemaRepository::class, ProductSchemaRepositoryEloquent::class);
        $this->app->bind(ProductSchemaTypeRepository::class, ProductSchemaTypeRepositoryEloquent::class);
        $this->app->bind(ProductSchemaRuleRepository::class, ProductSchemaRuleRepositoryEloquent::class);
        $this->registerControllers();

        $this->app->singleton('moduleProduct.product', function () {
            return new Product();
        });

        $this->app->bind('vcc.product.schema', SchemaService::class);
    }

    private function registerControllers()
    {
        $this->app->bind(ViewProductListControllerInterface::class, ViewProductListController::class);
        $this->app->bind(ViewProductDetailControllerInterface::class, ViewProductDetailController::class);
    }

    private function bootEntityName()
    {
        if (isset(config('product.models')['product'])) {
            $model       = config('product.models.product');
            $this->model = $model;
        } else {
            $this->model = BaseModel::class;
        }

        Relation::morphMap([
            'products'   => $this->model,
            'attributes' => Attribute::class,
            'variants'   => Variant::class,
            'schema'     => Schema::class,
        ]);
    }
}
