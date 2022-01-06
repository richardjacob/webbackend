<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverOnlineTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_online_times', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('driver_id');
            $table->date('online_date');
            $table->bigInteger('peak_time'); 
            $table->bigInteger('online_time');           
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
        Schema::dropIfExists('driver_online_times');
    }
}
