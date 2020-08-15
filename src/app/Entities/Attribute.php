<?php

namespace VCComponent\Laravel\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use VCComponent\Laravel\Product\Entities\AttributeValue;

class Attribute extends Model
{
    const KIND_DEFAULT  = 1;
    const KIND_EXTRA    = 2;

    protected $fillable = [
        'name',
        'type',
        'kind',
    ];

    public function attributeValue()
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id');
    }

}
