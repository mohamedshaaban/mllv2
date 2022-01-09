<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id');
            $table->integer('user_id');
            $table->integer('order_status_id');
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
        Schema::table('orders_logs', function (Blueprint $table) {
            //
        });
    }
}
