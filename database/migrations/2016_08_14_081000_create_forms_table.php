<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->integer('customer_id')->index();
            $table->string('access_code');
            $table->string('type');
            $table->text('data');
            $table->string('server_ip')->nullable();
            $table->string('user_ip_on_view')->nullable();
            $table->string('user_ip_on_sign')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->boolean('completed')->default(0);
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
        Schema::drop('forms');
    }
}
