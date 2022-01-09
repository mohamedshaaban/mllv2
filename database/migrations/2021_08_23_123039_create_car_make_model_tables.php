<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarMakeModelTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_makes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_en')->nullable();
            $table->string('name_ar')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('car_model', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_en')->nullable();
            $table->string('name_ar')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('car_makes', function (Blueprint $table) {
            //
        });
    }
}
