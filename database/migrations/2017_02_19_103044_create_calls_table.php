<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->increments('id');
            $table->string("CallID");
            $table->dateTime("Date")->nullable();
            $table->string("DID")->nullable();
            $table->string("CallerNumber");
            $table->string("CallerExtension")->nullable();
            $table->string("TargetNumber")->nullable();
            $table->string("TargetPrefixName")->nullable();
            $table->integer("Duration");
            $table->string("RepresentativeName")->nullable();
            $table->string("RepresentativeCode")->nullable();
            $table->string("DialStatus")->nullable();
            $table->string("DialStatus2")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calls');
    }
}
