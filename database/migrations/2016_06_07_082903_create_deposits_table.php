<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('broker_id')->default(1);
            $table->integer('deposits_crm_id');
            $table->integer("table_id")->nullable();
            $table->integer('customerId');
            $table->string('paymentMethod')->nullable();
            $table->string('clearedBy')->nullable();
            $table->integer('receptionEmployeeId')->index();
            $table->integer('processEmployeeId')->nullable();
            $table->integer('confirmEmployeeId')->nullable();
            $table->float('amount', 10, 2);
            $table->string('currency');
            $table->float('rateUSD');
            $table->float('amountUSD', 10, 2);
            $table->string('transactionID');
            $table->integer('campaignId')->nullable();
            $table->text('cancelReason')->nullable();
            $table->string('IPAddress');
            $table->timestamp('requestTime');
            $table->timestamp('confirmTime')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->timestamp('cancellationTime')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->string('type');
            $table->boolean("approved")->default(false);
            $table->string('status');
            $table->text('note')->nullable();
            $table->boolean('is_verified')->default(0);
            $table->integer("deposit_type")->default(0);
            $table->boolean("is_split")->default(0);
            $table->integer("split_id")->nullable();
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
        Schema::drop('deposits');
    }
}
