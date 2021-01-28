<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSchemasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_schemas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('label');
            $table->unsignedBigInteger('schema_type_id');
            $table->unsignedBigInteger('schema_rule_id');
            $table->string('product_type')->default('products');
            $table->foreign('schema_type_id')->references('id')->on('product_schema_types');
            $table->foreign('schema_rule_id')->references('id')->on('product_schema_rules');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_schemas');
    }
}
