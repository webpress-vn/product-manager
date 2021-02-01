<?php

namespace VCComponent\Laravel\Product\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductSchema extends Model
{
    protected $fillable = [
        'name',
        'label',
        'schema_type_id',
        'schema_rule_id',
        'product_type',
    ];

    public function schemaType()
    {
        return $this->beLongsTo(ProductSchemaType::class);
    }

    public function schemaRule()
    {
        return $this->beLongsTo(ProductSchemaRule::class);
    }

    public function scopeOfProductType($query, $product_type)
    {
        return $query->where('product_type', $product_type);
    }
}
