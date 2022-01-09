<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateFromToCustomerInvoicces extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('date_from')->nullable();
            $table->string('date_to')->nullable();
            $table->string('customer_id')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
        });
    }
}
