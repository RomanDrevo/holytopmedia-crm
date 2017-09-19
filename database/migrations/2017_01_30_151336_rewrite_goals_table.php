<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RewriteGoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->dropColumn('made_daily', 'made_monthly');
            $table->integer('table_id')->unsigned()->after("employee_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->integer('made_daily')->nullable()->after("monthly");
            $table->integer('made_monthly')->nullable()->after("made_daily");
            $table->dropColumn('table_id');
        });
    }
}
