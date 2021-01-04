<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use VCComponent\Laravel\Product\Entities\Product;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'name'           => $faker->words(rand(4, 5), true),
        'description'    => $faker->sentences(rand(4, 7), true),
        'quantity'       => $faker->randomNumber(),
        'code'           => $faker->swiftBicNumber,
        'price'          => $faker->randomNumber(),
        'original_price' => $faker->randomNumber(),
        'author_id'      => rand(1, 3),
        'thumbnail'      => $faker->imageUrl(),
        'sku'            => Str::random(32),
        'order'          => 0
    ];
});

$factory->state(Product::class, 'sim', function () {
    return [
        'product_type' => 'sim',
    ];
});
