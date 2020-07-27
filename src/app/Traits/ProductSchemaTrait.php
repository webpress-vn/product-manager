<?php

namespace VCComponent\Laravel\Product\Traits;

use VCComponent\Laravel\Product\Entities\ProductMeta;

trait ProductSchemaTrait
{
    public function productTypes()
    {
        return ['sim','drugs'];
    }

    public function productMetas()
    {
        return $this->hasMany(ProductMeta::class);
    }

    public function schema()
    {
        return [];
    }
}
