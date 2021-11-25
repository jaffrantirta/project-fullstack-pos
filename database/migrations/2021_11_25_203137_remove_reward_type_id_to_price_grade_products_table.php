<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveRewardTypeIdToPriceGradeProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('price_grade_products', function (Blueprint $table) {
            $table->dropForeign('price_grade_products_reward_type_id_foreign');
            $table->dropColumn('reward_type_id');
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
