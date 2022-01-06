<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_remarks', function (Blueprint $table) {
            $table->id(); 
            $table->integer('hub_employee_id')->unsigned(); // user who shares their referral code
            $table->foreign('hub_employee_id')->references('id')->on('hub_employees');

            $table->integer('driver_id')->unsigned();
            $table->foreign('driver_id')->references('id')->on('users');

            $table->text('conversation')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('remarks_date')->nullable();
            $table->timestamp('followup_date')->nullable();
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
        Schema::dropIfExists('driver_remarks');
    }
}
