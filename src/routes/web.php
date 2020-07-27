<?php

if (config('product.models.product') !== null) {
    $model_class = config('product.models.product');
} else {
    $model_class = VCComponent\Laravel\Product\Entities\Product::class;
}

$model        = new $model_class;
$productTypes = $model->productTypes();

Route::prefix(config('product.namespace'))
    ->middleware('web')
    ->group(function () use ($productTypes) {
        Route::get('/products', 'VCComponent\Laravel\Product\Contracts\ViewProductListControllerInterface@index');
        Route::get('/products/{slug}', 'VCComponent\Laravel\Product\Contracts\ViewProductDetailControllerInterface@show');
        if (count($productTypes)) {
            foreach ($productTypes as $productType) {
                Route::get($productType, 'VCComponent\Laravel\Product\Contracts\ViewProductListControllerInterface@index');
                Route::get($productType.'/{slug}', 'VCComponent\Laravel\Product\Contracts\ViewProductDetailControllerInterface@show');
            }
        }
    });
