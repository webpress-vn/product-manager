<?php

namespace VCComponent\Laravel\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use VCComponent\Laravel\Product\Entities\Product;

class ProductMeta extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'product_meta';

    protected $fillable = [
        'key',
        'value',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
