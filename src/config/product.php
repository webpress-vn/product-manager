<?php

return [

    'namespace'       => env('PRODUCT_COMPONENT_NAMESPACE', ''),

    'models'          => [
        'product' => VCComponent\Laravel\Product\Entities\Product::class,
    ],

    'transformers'    => [
        'product' => VCComponent\Laravel\Product\Transformers\ProductTransformer::class,
    ],

    'viewModels'      => [
        'productList'   => VCComponent\Laravel\Product\ViewModels\ProductList\ProductListViewModel::class,
        'productDetail' => VCComponent\Laravel\Product\ViewModels\ProductDetail\ProductDetailViewModel::class,
    ],
    'auth_middleware' => [
        'admin'    => [],
        'frontend' => [],
    ],
    'cache'           => [
        'enabled' => false,
        'minutes' => 1,
    ],
    'test_mode' => false,

    'config_product'  => [
        'attribute_product' => false,
        'combo_product'     => false,
    ],
];
