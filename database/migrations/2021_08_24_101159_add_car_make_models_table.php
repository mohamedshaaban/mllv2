<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCarMakeModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_model', function (Blueprint $table) {
            $table->string('car_make')->nullable();
            $table->string('name_en')->change()->unique();
            $table->string('name_ar')->change()->unique();
        });

        Schema::table('car_makes', function (Blueprint $table) {
            $table->string('name_en')->change()->unique();
            $table->string('name_ar')->change()->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('car_model', function (Blueprint $table) {
            //
        });
    }
}
