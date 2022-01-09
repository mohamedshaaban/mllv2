<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nationality')->nullable();
            $table->string('mobile')->nullable();
            $table->string('photo')->nullable();
            $table->string('civil_front_id')->nullable();
            $table->string('civil_back_id')->nullable();
            $table->string('license_front_id')->nullable();
            $table->string('license_back_id')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
