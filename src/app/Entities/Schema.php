<?php

namespace VCComponent\Laravel\Product\Entities;

use Illuminate\Database\Eloquent\Model;

class Schema extends Model
{
    protected $fillable = [
        'name',
        'type',
        'rule',
        'product_type'
    ];
}
