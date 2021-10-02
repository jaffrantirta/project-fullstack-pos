<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePremiumStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('premium_statuses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('premium_id')->unsigned();
            $table->bigInteger('premium_status_type_id')->unsigned();
            $table->timestamps();
            $table->foreign('premium_id')->references('id')->on('premiums')->onDelete('cascade');
            $table->foreign('premium_status_type_id')->references('id')->on('premium_status_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('premium_statuses');
    }
}
