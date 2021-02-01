<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use VCComponent\Laravel\Product\Entities\ProductSchemaRule;

class SeedProductSchemaRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        ProductSchemaRule::insert([
            [
                "name" => "E-mail",
            ],
            [
                "name" => "Date",
            ],
            [
                "name" => "Nullable",
            ],
            [
                "name" => "File",
            ],
            [
                "name" => "Required",
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
