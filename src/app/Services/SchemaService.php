<?php

namespace VCComponent\Laravel\Product\Services;

use Illuminate\Support\Collection;
use VCComponent\Laravel\Product\Entities\ProductSchema;

/**
 * Class contains schema helper functions
 */
class SchemaService
{
    public function get(string $product_type): Collection
    {
        return ProductSchema::ofProductType($product_type)->get();
    }

    public function getKey(string $product_type): Collection
    {
        $data = $this->get($product_type);
        return $data->map(function ($item) {
            return $item->name;
        });
    }
}
