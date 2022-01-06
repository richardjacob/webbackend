<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBonusTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonus_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_balance_id')->nullable();
            $table->foreign('driver_balance_id')->references('id')->on('driver_balances');
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->decimal('amount',11,2);
            $table->date('transaction_date');
            $table->string('transaction_id',20)->nullable();
            $table->string('payout_type')->nullable();
            $table->unsignedInteger('payout_id')->nullable();
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
        Schema::dropIfExists('bonus_transactions');
    }
}
