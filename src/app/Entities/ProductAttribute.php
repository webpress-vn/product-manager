<?php

namespace VCComponent\Laravel\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use VCComponent\Laravel\Product\Entities\AttributeValue;

class ProductAttribute extends Model
{
    const PRICE_ORIGIN = 3;
    const PRICE_PLUS   = 1;
    const PRICE_MINUS  = 2;

    protected $fillable = [
        'product_id',
        'value_id',
        'type',
        'price',
    ];

    public function attributeItem()
    {
        return $this->beLongsTo(AttributeValue::class, 'value_id', 'id');
    }
}
