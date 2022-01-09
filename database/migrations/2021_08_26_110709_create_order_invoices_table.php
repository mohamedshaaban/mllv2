<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->nullable();
            $table->integer('invoice_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
              $table->integer('is_paid')->default(0);
            $table->string('url');
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
        Schema::table('order_invoices', function (Blueprint $table) {
            //
        });
    }
}
