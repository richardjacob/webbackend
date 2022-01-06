<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complains', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cat_id');
            $table->foreign('cat_id')->references('id')->on('complain_categories')->onDelete('cascade');
            $table->unsignedBigInteger('sub_cat_id');
            $table->foreign('sub_cat_id,')->references('id')->on('complain_sub_categories')->onDelete('cascade');            
            $table->enum('complain_by',['Rider', 'Driver'])->default('Rider');  

            $table->unsignedInteger('rider_id');
            $table->foreign('rider_id')->references('id')->on('users')->onDelete('cascade'); 

            $table->unsignedInteger('driver_id');
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedInteger('vehicle_id');
            $table->foreign('vehicle_id')->references('id')->on('vehicle')->onDelete('cascade');

            $table->string('vehicle_number', '255');

            $table->unsignedInteger('trip_id');
            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');

            $table->text('pickup_location')->nullable();
            $table->text('drop_location')->nullable();
            $table->text('complain_content');
            $table->enum('status',['0', '1', '2'])->default('0')->comment('0=Pending, 1=Completed, 2=Processing'); 
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
        Schema::dropIfExists('complains');
    }
}
