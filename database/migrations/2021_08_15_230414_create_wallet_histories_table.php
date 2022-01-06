<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_histories', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('user_type',['Driver', 'Rider']);
            $table->text('bonus_type',100)->nullable();
            $table->unsignedBigInteger('bonus_id')->nullable();
            $table->foreign('bonus_id')->references('id')->on('bonuses')->onDelete('cascade');
            $table->decimal('amount',11,2);            
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
        Schema::dropIfExists('wallet_histories');
    }
}
