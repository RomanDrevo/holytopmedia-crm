<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_customers', function (Blueprint $table) {
            $table->increments('id'); 
            $table->string('list_id');
            $table->string('customer_id');
            $table->string('name');
            $table->string('employee_id')->nullable();
            $table->string('employee_name')->nullable();
            $table->boolean('clicked')->default(0);
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
        Schema::dropIfExists('email_customers');
    }
}
