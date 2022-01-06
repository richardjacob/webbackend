<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitorCamerasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitor_cameras', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('driver_id')->nullable();
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('vehicle_id')->nullable();
            $table->foreign('vehicle_id')->references('id')->on('vehicle')->onDelete('cascade');
            $table->text('monitor_sim',50);
            $table->text('monitor_imei',50);
            $table->text('monitor_ip',50);
            $table->enum('monitor_status',['Active', 'Inactive','Problem','Not Connected'])->default('Inactive');
            $table->text('camera_sim',50);
            $table->text('camera_imei',50);
            $table->text('camera_ip',50);
            $table->enum('camera_status',['Active', 'Inactive','Problem','Not Connected'])->default('Inactive');
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
        Schema::dropIfExists('monitor_cameras');
    }
}
