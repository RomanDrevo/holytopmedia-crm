<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('broker_id')->default(1);
            $table->integer("customer_crm_id");
            $table->string('FirstName');
            $table->string('LastName');
            $table->string('email');
            $table->string('secondary_email')->nullable();
            $table->string('Phone');
            $table->string('secondary_phone')->nullable();
            $table->integer('Country');
            $table->integer('registrationCountry');
            $table->integer('campaignId');
            $table->integer('subCampaignId');
            $table->timestamp('regTime')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->string('currency');
            $table->integer('employeeInChargeId');
            $table->string('regStatus');
            $table->timestamp('firstDepositDate')->nullable();
            $table->string('verification');
            $table->timestamp('lastLoginDate')->nullable();
            $table->timestamp('lastDepositDate')->nullable();
            $table->timestamp('lastWithdrawalDate')->nullable();
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
        Schema::drop('customers');
    }
}
