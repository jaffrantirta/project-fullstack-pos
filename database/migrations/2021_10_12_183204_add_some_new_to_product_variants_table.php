<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeNewToProductVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('sku');
            $table->double('purchase_price');
            $table->double('selling_price');
            $table->boolean('is_empty');
            $table->integer('stock');
            $table->boolean('is_notification_alert');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('sku');
            $table->dropColumn('purchase_price');
            $table->dropColumn('selling_price');
            $table->dropColumn('is_empty');
            $table->dropColumn('stock');
            $table->dropColumn('is_notification_alert');
            $table->float('commission')->default(0)->change();
            $table->integer('minimum_purchase')->default(1)->change();
            $table->integer('maximum_purchase')->nullable()->change();
            $table->boolean('is_dynamic_price')->default(false)->change();
            $table->string('picture')->nullable()->change();
            $table->boolean('is_tax')->default(false)->change();
            $table->text('description')->nullable()->change();
            $table->text('note')->nullable()->change();
            $table->boolean('is_show')->default(true)->change();
            $table->boolean('is_active')->default(true)->change();
            $table->bigInteger('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_variants', function (Blueprint $table) {
            //
        });
    }
}
