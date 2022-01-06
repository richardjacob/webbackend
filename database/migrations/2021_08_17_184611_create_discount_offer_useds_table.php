<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountOfferUsedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_offer_used', function (Blueprint $table) {
            $table->id();
            $table->enum('user_type',['Driver', 'Rider']);
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('trip_id')->nullable();
            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');
            $table->string('type',100)->nullable();            
            $table->string('start_time',30)->nullable();       
            $table->string('end_time',30)->nullable();  
            $table->string('amount',20)->nullable();      
            $table->decimal('discount_amount',11,2); 
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
        Schema::dropIfExists('discount_offer_used');
    }
}
