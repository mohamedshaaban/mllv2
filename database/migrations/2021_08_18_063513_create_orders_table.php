<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_unique_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('car_id')->nullable();
            $table->integer('area_from')->nullable();
            $table->integer('area_to')->nullable();
            $table->integer('driver_id')->nullable();
            $table->integer('status')->nullable();
            $table->text('address')->nullable();
            $table->integer('paid_by')->nullable();
            $table->text('comission')->nullable();
            $table->integer('comission_paid')->nullable();
            $table->string('date')->nullable();
            $table->string('time')->nullable();
            $table->string('amount')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('is_paid')->nullable();
            $table->string('payment_link')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('payment_transaction', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('order_id');
            $table->string('transaction_id');
            $table->string('refernece_number');
            $table->string('amount');
            $table->string('status');
            $table->string('date');
            $table->longText('response');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('payment_history', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('order_id');
            $table->integer('payment_transaction_id');
            $table->string('status');
            $table->string('payment_type');
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
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
}
