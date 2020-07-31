<?php

namespace VCComponent\Laravel\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use VCComponent\Laravel\Product\Entities\Product;
use VCComponent\Laravel\Product\Entities\VariantProduct;
use VCComponent\Laravel\Product\Traits\HasVariantTrait;
use VCComponent\Laravel\Product\Traits\ProductManagementTrait;

class Variant extends Model
{
    use ProductManagementTrait, HasVariantTrait;

    protected $fillable= [
        'id',
        'label',
        'thumbnail',
        'price',
        'type',
        'product_id',
        'status',
    ];


}
