<?php

namespace VCComponent\Laravel\Product\Facades;

use Illuminate\Support\Facades\Facade;

class Schema extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'vcc.product.schema';
    }
}
