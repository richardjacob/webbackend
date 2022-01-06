<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeakHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peak_hours', function (Blueprint $table) {
            $table->id();
            $table->enum('type',['1', '2']);
            $table->string('day_name');
            $table->time("start_time");
            $table->time("end_time");
            $table->enum('status',['0', '1'])->default('0');
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
        Schema::dropIfExists('peak_hours');
    }
}
