<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplaintSubCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complain_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('complain_cat_id');
            $table->foreign('complain_cat_id')->references('id')->on('complain_categories')->onDelete('cascade');
            $table->string('sub_category');
            $table->text('sub_category_bn');
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
        Schema::dropIfExists('complain_sub_categories');
    }
}
