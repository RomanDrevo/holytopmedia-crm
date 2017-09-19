<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWithdrawalSplitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdrawal_splits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id');
            $table->integer("withdrawal_id");
            $table->integer("from_employee_id");
            $table->float('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('withdrawal_splits');
    }
}
