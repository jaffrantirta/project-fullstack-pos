<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceGradeProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_grade_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->unsigned();
            $table->bigInteger('buyer_type_id')->unsigned();
            $table->bigInteger('reward_type_id')->unsigned();
            $table->integer('minimum_qty');
            $table->double('selling_price')->nullable();
            $table->float('discount')->nullable();
            $table->bigInteger('product_obtained')->nullable()->unsigned();
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
        Schema::dropIfExists('price_grade_products');
    }
}
