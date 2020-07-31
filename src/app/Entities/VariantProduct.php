<?php

namespace VCComponent\Laravel\Product\Entities;

use Illuminate\Database\Eloquent\Model;

class VariantProduct extends Model
{
    protected $fillable = [
        'id',
        'variant_id',
        'variantable_id',
        'variantable_type',
    ];
}
