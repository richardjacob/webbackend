<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplainMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complain_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('complain_id');
            $table->foreign('complain_id')->references('id')->on('complains')->onDelete('cascade');
            $table->text('process');
            $table->string('process_by', '255');
            $table->text('remarks');

            $table->unsignedInteger('entry_by_id');
            $table->foreign('entry_by_id')->references('id')->on('admins')->onDelete('cascade');
            
            $table->unsignedInteger('updated_by_id');
            $table->foreign('updated_by_id')->nullable()->references('id')->on('admins')->onDelete('cascade');
            $table->enum('status',['1', '2'])->default('2')->comment('1=Completed, 2=Processing'); 

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
        Schema::dropIfExists('complain_movements');
    }
}
