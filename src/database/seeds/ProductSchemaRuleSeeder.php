<?php

use Illuminate\Database\Seeder;
use VCComponent\Laravel\Product\Entities\ProductSchemaRule;

class ProductSchemaRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        ProductSchemaRule::insert([
            [
                "name" => "E-mail"
            ],
            [
                "name" => "Date"
            ],
            [
                "name" => "Nullable"
            ],
            [
                "name" => "File"
            ],
            [
                "name" => "Required"
            ]
        ]);
    }
}
