<?php

namespace VCComponent\Laravel\Product\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductSchemaType extends Model
{
    protected $fillable = [
        'id',
        'name'
    ];

    public function schema() {
        return $this->hasMany(ProductSchema::class, 'schema_type_id');
    }
}
