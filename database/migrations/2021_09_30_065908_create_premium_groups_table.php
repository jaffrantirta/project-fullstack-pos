<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePremiumGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('premium_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->text('picture')->nullable();
            $table->timestamps();
        });
        Schema::table('premiums', function (Blueprint $table) {
            $table->foreign('premium_group_id')->references('id')->on('premium_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('premium_groups');
    }
}
