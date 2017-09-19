<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActiveToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->boolean("active")->default(1)->after("name");
            $table->dropColumn("manager");
            $table->integer("manager_id")->unsigned()->nullable()->after("broker_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn("active", "manager_id");
            $table->string("manager")->after("name");
        });
    }
}
