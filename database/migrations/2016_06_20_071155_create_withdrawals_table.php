<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('broker_id')->default(1);
            $table->integer('withdrawals_crm_id');
            $table->integer("table_id")->nullable();
            $table->boolean('is_verified')->default(0);
            $table->boolean('is_split')->default(0);
            $table->integer('withdrawal_split_id')->nullable();
            $table->string('paymentMethod');
            $table->string('confirmationCode');
            $table->float('amount', 10, 2);
            $table->timestamp('requestTime');
            $table->timestamp('confirmTime')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->string('status');
            $table->timestamp('cancellationTime')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->string('currency');
            $table->float('rateUSD');
            $table->float('amountUSD', 10, 2);
            $table->text('cancelReason')->nullable();
            $table->integer('receptionEmployeeId')->nullable()->index();
            $table->integer('processEmployeeId')->nullable();
            $table->integer('confirmEmployeeId')->nullable();
            $table->integer('customerId')->index();
            $table->string('type');
            $table->boolean("approved")->default(false);
            $table->integer('clearingUserID');
            $table->string('clearedBy')->default('');
            $table->string('transactionID');
            $table->integer('campaignId')->nullable();
            $table->string('swiftCode')->nullable();
            $table->integer("withdrawal_type")->default(0);
            $table->string('comment')->nullable();
            $table->string('description')->nullable();
            $table->integer('waitForDocs');
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
        Schema::drop('withdrawals');
    }
}
