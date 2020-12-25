<?php

namespace VCComponent\Laravel\Product\Test\Stubs\Models;

use VCComponent\Laravel\Product\Entities\Product as BaseProduct;

class Product extends BaseProduct
{
    public function schema()
    {
        return [
            'address' => [
                'type' => 'string',
                'rule' => [],
            ],
        ];
    }

    public function productTypes()
    {
        return [
            'testTypes',
        ];
    }

    public function aboutSchema()
    {
        return [];
    }

    public function testTypesSchema()
    {
         return [
            'testField' => [
                'type' => 'string',
                'rule' => [],
            ],
        ];
    }
}