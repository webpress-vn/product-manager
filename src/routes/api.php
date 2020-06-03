    <?php

if (config('product.models.product') !== null) {
    $model_class = config('product.models.product');
} else {
    $model_class = VCComponent\Laravel\Product\Entities\Product::class;
}

$model = new $model_class;
$api   = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->group(['prefix' => config('product.namespace')], function ($api) {
        $api->group(['prefix' => 'admin'], function ($api) {
            $api->get('products/export', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@exportExcel');
            $api->get('products/all', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@list');
            $api->put('products/status/bulk', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@bulkUpdateStatus');
            $api->put('products/status/{id}', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@updateStatusItem');
            $api->resource('products', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController');
            $api->put('product/{id}/date', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@changeDatetime');
            $api->get('product/{id}/stock', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@checkStock');
            $api->put('product/{id}/quantity', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@updateQuantity');
            $api->put('product/{id}/change_quantity', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@changeQuantity');
        });
        $api->get('products/all', 'VCComponent\Laravel\Product\Http\Controllers\Api\Frontend\ProductController@list');
        $api->put('products/status/bulk', 'VCComponent\Laravel\Product\Http\Controllers\Api\Frontend\ProductController@bulkUpdateStatus');
        $api->put('products/{id}/status', 'VCComponent\Laravel\Product\Http\Controllers\Api\Frontend\ProductController@updateStatusItem');
        $api->resource('products', 'VCComponent\Laravel\Product\Http\Controllers\Api\Frontend\ProductController');
    });
});
