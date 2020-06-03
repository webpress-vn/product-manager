<?php

Route::prefix(config('product.namespace'))
    ->middleware('web')
    ->group(function () {
        Route::get('/products', 'VCComponent\Laravel\Product\Contracts\ViewProductListControllerInterface@index');
        Route::get('/products/{slug}', 'VCComponent\Laravel\Product\Contracts\ViewProductDetailControllerInterface@show');
    });
