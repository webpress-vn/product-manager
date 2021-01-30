<?php

use Faker\Generator as Faker;
use VCComponent\Laravel\Product\Entities\ProductSchema;

$factory->define(ProductSchema::class, function (Faker $faker) {
    return [
        'name'           => 'phone',
        'label'          => 'Phone',
        'schema_type_id' => 1,
        'schema_rule_id' => 5,
    ];
});
