<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopCategoryOwnedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_category_owneds', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('shop_id')->unsigned();
            $table->bigInteger('shop_category_id')->unsigned();
            $table->timestamps();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('shop_category_id')->references('id')->on('shop_categories')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_category_owneds');
    }
}
