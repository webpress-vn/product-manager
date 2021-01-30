<?php

if (config('product.models.product') !== null) {
    $model_class = config('product.models.product');
} else {
    $model_class = VCComponent\Laravel\Product\Entities\Product::class;
}

$model        = new $model_class;
$productTypes = $model->productTypes();

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) use ($productTypes) {
    $api->group(['prefix' => config('product.namespace')], function ($api) use ($productTypes) {
        $api->group(['prefix' => 'admin'], function ($api) use ($productTypes) {
            $api->get('products/field-meta', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@getFieldMeta');
            $api->get('products/exports', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@export');
            $api->delete('products/bulk', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@bulkDelete');
            $api->delete('products/{id}/force', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@forceDelete');
            $api->delete('products/force/bulk', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@forceBulkDelete');

            $api->delete('products/trash/all', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@deleteAllTrash');
            $api->delete('products/trash/bulk', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@bulkDeleteTrash');
            $api->delete('products/trash/{id}', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@deleteTrash');

            $api->get('products/trash/all', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@getAllTrash');

            $api->get('products/trash', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@trash');

            $api->put('products/trash/bulk/restores', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@bulkRestore');

            $api->put('products/trash/{id}/restore', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@restore');

            $api->get('products/all', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@list');

            $api->put('products/status/bulk', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@bulkUpdateStatus');

            $api->put('product/{id}/status', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@updateStatusItem');

            $api->resource('products', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController');
            $api->put('product/{id}/date', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@changeDatetime');
            $api->get('product/{id}/stock', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@checkStock');
            $api->put('product/{id}/quantity', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@updateQuantity');
            $api->put('product/{id}/change_quantity', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@changeQuantity');

            $api->resource('attributes', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\AttributeController');
            $api->resource('attribute-value', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\AttributeValueController');
            $api->post('attributes/{id}/language', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\AttributeController@storeTranslateLanguage');
            $api->delete('attributes/{id}/language', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\AttributeController@destroyTranslateLanguage');
            $api->post('attribute-value/{id}/language', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\AttributeValueController@storeTranslateLanguage');
            $api->delete('attribute-value/{id}/language', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\AttributeValueController@destroyTranslateLanguage');

            $api->get('variants/list', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\VariantController@list');
            $api->resource('variants', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\VariantController');
            $api->put("/variant/{id}/status", "VCComponent\Laravel\Product\Http\Controllers\Api\Admin\VariantController@updateStatus");

            $api->get('productTypes', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@getType');

//            $api->get('schemas', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductSchemaController@index');
//            $api->get('schema/{id}', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductSchemaController@show');
//            $api->post('schema', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductSchemaController@store');
//            $api->put('schema/{id}', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductSchemaController@update');
//            $api->delete('schema/{id}', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductSchemaController@destroy');

            $api->resource('schemas', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductSchemaController');

            $api->get('schema-types', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\SchemaTypeController@index');
            $api->get('schema-types/all', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\SchemaTypeController@list');

            $api->get('schema-rules', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\SchemaRuleController@index');
            $api->get('schema-rules/all', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\SchemaRuleController@list');

            if (count($productTypes)) {
                foreach ($productTypes as $productType) {
                    $api->get($productType . '/exports', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@export');
                    $api->get($productType . '/field-meta', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@getFieldMeta');
                    $api->delete($productType . '/force/bulk', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@forceBulkDelete');
                    $api->delete($productType . '/bulk', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@bulkDelete');
                    $api->delete($productType . '/{id}/force', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@forceDelete');
                    $api->delete($productType . '/trash/all', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@deleteAllTrash');
                    $api->delete($productType . '/trash/bulk', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@bulkDeleteTrash');
                    $api->delete($productType . '/trash/{id}', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@deleteTrash');
                    $api->get($productType . '/trash/all', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@getAllTrash');
                    $api->get($productType . '/trash', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@trash');
                    $api->put($productType . '/trash/bulk/restores', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@bulkRestore');
                    $api->put($productType . '/trash/{id}/restore', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@restore');
                    $api->get($productType . '/all', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@list');
                    $api->put($productType . '/status/bulk', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@bulkUpdateStatus');
                    $api->put($productType . '/{id}/status', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@updateStatusItem');
                    $api->resource($productType, 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController');
                    $api->put($productType . '/{id}/date', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@changeDatetime');
                    $api->get($productType . '/{id}/stock', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@checkStock');
                    $api->put($productType . '/{id}/quantity', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@updateQuantity');
                    $api->put($productType . '/{id}/change_quantity', 'VCComponent\Laravel\Product\Http\Controllers\Api\Admin\ProductController@changeQuantity');
                }
            }
        });
        $api->get('products/all', 'VCComponent\Laravel\Product\Http\Controllers\Api\Frontend\ProductController@list');
        $api->put('products/status/bulk', 'VCComponent\Laravel\Product\Http\Controllers\Api\Frontend\ProductController@bulkUpdateStatus');
        $api->put('product/{id}/status', 'VCComponent\Laravel\Product\Http\Controllers\Api\Frontend\ProductController@updateStatusItem');
        $api->resource('products', 'VCComponent\Laravel\Product\Http\Controllers\Api\Frontend\ProductController');

        if (count($productTypes)) {
            foreach ($productTypes as $productType) {
                $api->get($productType . '/all', 'VCComponent\Laravel\Product\Http\Controllers\Api\Frontend\ProductController@list');
                $api->put($productType . '/status/bulk', 'VCComponent\Laravel\Product\Http\Controllers\Api\Frontend\ProductController@bulkUpdateStatus');
                $api->put($productType . '/{id}/status', 'VCComponent\Laravel\Product\Http\Controllers\Api\Frontend\ProductController@updateStatusItem');
                $api->resource($productType . '', 'VCComponent\Laravel\Product\Http\Controllers\Api\Frontend\ProductController');
            }
        }
    });
});
