<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBonusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('bonuses', function (Blueprint $table) {
        //     $table->integer('id')->primary();
        //     $table->integer('customer_id')->index();
        //     $table->string('transaction_id');
        //     $table->integer('confirm_employee_id');
        //     $table->string('payment_method')->nullable();
        //     $table->string('cleared_by')->nullable();
        //     $table->float('amount');
        //     $table->string('currency');
        //     $table->dateTime('confirm_time');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::drop('bonuses');
    }
}
