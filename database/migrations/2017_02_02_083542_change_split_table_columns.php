<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSplitTableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('splits', function (Blueprint $table) {
            $table->dropColumn('employee_id', 'from_employee_id');
            $table->integer("to")->unsigned()->index()->after("deposit_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('splits', function (Blueprint $table) {
            //
        });
    }
}
