<?php

namespace VCComponent\Laravel\Product\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductSchemaRule extends Model
{
    protected $fillable = [
        'id',
        'name'
    ];

    public function schema() {
        return $this->hasMany(ProductSchema::class, 'schema_rule_id');
    }
}
