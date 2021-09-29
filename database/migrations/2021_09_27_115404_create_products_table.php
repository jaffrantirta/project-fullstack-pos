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
            $table->id();
            $table->string('name');
            $table->string('sku');
            $table->double('purchase_price');
            $table->double('selling_price');
            $table->float('commission');
            $table->integer('minimum_purchase');
            $table->integer('maximum_purchase');
            $table->boolean('is_dynamic_price');
            $table->boolean('is_empty');
            $table->string('picture');
            $table->boolean('is_tax');
            $table->text('description');
            $table->text('note');
            $table->boolean('is_show');
            $table->boolean('is_active');
            $table->integer('stock');
            $table->boolean('is_notification_alert');
            $table->bigInteger('shop_id')->unsigned();
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
        Schema::dropIfExists('products');
    }
}
