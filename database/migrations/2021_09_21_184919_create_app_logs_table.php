<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_logs', function (Blueprint $table) {
            $table->id();
            $table->string('log_type');
            $table->string('user_id');
            $table->string('action_name');
            $table->string('detail');
            $table->string('page_name');
            $table->string('button_name');
            $table->string('comment');
            $table->text('phone_full_info');
            $table->string('platform');
            $table->string('app_version');
            $table->string('is_debug');
            $table->string('trip_id');
            $table->text('token_login_auth');
            $table->text('token_firebase');
            $table->string('server_date_time');
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
        Schema::dropIfExists('app_logs');
    }
}
