<?php

use Illuminate\Database\Seeder;
use VCComponent\Laravel\Product\Entities\ProductSchemaType;

class ProductSchemaTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        ProductSchemaType::insert([
            [
                "name" => "text"
            ],
            [
                "name" => "textarea"
            ],
            [
                "name" => "tinyMCE"
            ]
        ]);
    }
}
