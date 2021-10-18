<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductVariantIdToOrderDetailSellingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_detail_sellings', function (Blueprint $table) {
            $table->bigInteger('product_variant_id')->unsigned();
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_detail_sellings', function (Blueprint $table) {
            //
        });
    }
}
