<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeWithdrawalSplitTableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('withdrawal_splits', function (Blueprint $table) {
            $table->dropColumn('employee_id', 'from_employee_id');
            $table->integer("to")->unsigned()->index()->after("withdrawal_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('withdrawal_splits', function (Blueprint $table) {
            $table->integer('employee_id')->after("withdrawal_id");
            $table->integer("from_employee_id")->after("employee_id");
        });
    }
}
