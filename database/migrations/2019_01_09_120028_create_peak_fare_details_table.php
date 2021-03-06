<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeakFareDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peak_fare_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fare_id')->unsigned();
            $table->foreign('fare_id')->references('id')->on('manage_fare');
            $table->enum('type',['Peak', 'Night']);
            $table->tinyInteger('day')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('price', 10, 2);
            $table->index(['day','start_time','end_time']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('peak_fare_details');
    }
}
