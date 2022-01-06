<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nid', function (Blueprint $table) {
            $table->id();
            $table->string('name_en')->nullable();
            $table->string('father_en')->nullable();
            $table->string('mother_en')->nullable();
            $table->string('spouse_en')->nullable();
            $table->string('permanent_address_en')->nullable();
            $table->string('present_address_en')->nullable();

            $table->text('name')->nullable();
            $table->text('father')->nullable();
            $table->text('mother')->nullable();
            $table->text('spouse')->nullable();            
            $table->enum('gender',[1,2,3])->nullable();
            $table->date('dob')->nullable();
            $table->text('permanent_address')->nullable();
            $table->text('present_address')->nullable();
            $table->text('photo')->nullable(); //blob
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
        Schema::dropIfExists('nids');
    }
}
