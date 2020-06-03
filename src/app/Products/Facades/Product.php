<?php
namespace VCComponent\Laravel\Product\Products\Facades;

use Illuminate\Support\Facades\Facade;

class Product extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'moduleProduct.product';
    }
}
