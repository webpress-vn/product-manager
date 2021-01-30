<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use VCComponent\Laravel\Product\Entities\ProductSchemaType;

class SeedProductSchemaTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        ProductSchemaType::insert([
            [
                "name" => "text",
            ],
            [
                "name" => "textarea",
            ],
            [
                "name" => "tinyMCE",
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('product_schema_types')->truncate();
    }
}
