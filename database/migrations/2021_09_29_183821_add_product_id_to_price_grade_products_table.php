<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductIdToPriceGradeProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('price_grade_products', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('buyer_type_id')->references('id')->on('buyer_types')->onDelete('cascade');
            $table->foreign('reward_type_id')->references('id')->on('reward_types')->onDelete('cascade');
            $table->foreign('product_obtained')->references('id')->on('products')->onDelete('cascade')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('price_grade_products', function (Blueprint $table) {
            //
        });
    }
}
