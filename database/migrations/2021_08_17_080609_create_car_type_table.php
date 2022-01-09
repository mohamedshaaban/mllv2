<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_en')->nullable();
            $table->string('name_ar')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('car_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_en')->nullable();
            $table->string('name_ar')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('mobile')->nullable();
            $table->integer('status')->nullable();
            $table->integer('type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cars', function (Blueprint $table) {
            $table->increments('id');
            $table->string('car_plate_id')->nullable();
            $table->string('car_model')->nullable();
            $table->integer('car_type_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('xero_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('request_id')->nullable();
            $table->string('request_body')->nullable();
            $table->string('response_body')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('request_status', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_en')->nullable();
            $table->string('name_ar')->nullable();
            $table->string('image')->nullable();

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
        Schema::table('car_types', function (Blueprint $table) {
            //
        });
    }
}
