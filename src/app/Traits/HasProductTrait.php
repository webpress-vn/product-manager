<?php

namespace VCComponent\Laravel\Product\Traits;

use Illuminate\Http\Request;
use VCComponent\Laravel\Product\Entities\AttributeValue;
use VCComponent\Laravel\Product\Entities\Product;
use VCComponent\Laravel\Product\Entities\ProductAttribute;

trait HasProductTrait
{
    public function attributesProduct()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function products()
    {
        return $this->morphedByMany(Product::class, 'categoryable');
    }

    public function attributeValue()
    {
        return $this->hasOne(AttributeValue::class, 'id', 'value_id');
    }
}
