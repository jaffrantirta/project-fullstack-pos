<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShopIdToBuyerTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('buyer_types', function (Blueprint $table) {
            $table->bigInteger('shop_id')->unsigned();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->bigInteger('shop_id')->unsigned();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
        Schema::table('discount_types', function (Blueprint $table) {
            $table->bigInteger('shop_id')->unsigned();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
        Schema::table('groups', function (Blueprint $table) {
            $table->bigInteger('shop_id')->unsigned();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
        Schema::table('logs', function (Blueprint $table) {
            $table->bigInteger('shop_id')->unsigned();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
        Schema::table('loyalties', function (Blueprint $table) {
            $table->bigInteger('shop_id')->unsigned();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->bigInteger('shop_id')->unsigned();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
        Schema::table('order_types', function (Blueprint $table) {
            $table->bigInteger('shop_id')->unsigned();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->bigInteger('shop_id')->unsigned();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
        Schema::table('returns', function (Blueprint $table) {
            $table->bigInteger('shop_id')->unsigned();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
        Schema::table('reward_types', function (Blueprint $table) {
            $table->bigInteger('shop_id')->unsigned();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
        Schema::table('suppliers', function (Blueprint $table) {
            $table->bigInteger('shop_id')->unsigned();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
        Schema::table('voucher_types', function (Blueprint $table) {
            $table->bigInteger('shop_id')->unsigned();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
        Schema::table('vouchers', function (Blueprint $table) {
            $table->bigInteger('shop_id')->unsigned();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('buyer_types', function (Blueprint $table) {
            //
        });
    }
}
