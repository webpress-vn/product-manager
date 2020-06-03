<?php

namespace VCComponent\Laravel\Product\Entities;

use Illuminate\Database\Eloquent\Model;

class UserProduct extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'product_id',
    ];
}
