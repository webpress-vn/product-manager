<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug');
            $table->string('quantity');
            $table->string('sold_quantity')->default(0);
            $table->string('code')->nullable();
            $table->string('brand')->nullable();
            $table->string('thumbnail')->nullable();
            $table->integer('order')->default(0);
            $table->integer('status')->default(1);
            $table->text('description')->nullable();
            $table->integer('price')->default(0);
            $table->integer('original_price')->default(0);
            $table->string('unit_price')->default('đ');
            $table->tinyInteger('is_hot')->default(0);
            $table->unsignedBigInteger('author_id');
            $table->dateTime('published_date')->useCurrent();
            $table->string('sku')->unique()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
