<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('attendance_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('attendance_id');

            // 追加されたカラム（統合）
            $table->date('attendance_date')->nullable();
            $table->time('request_start_time')->nullable();
            $table->time('request_end_time')->nullable();

            $table->string('reason');
            $table->time('break_start')->nullable();
            $table->time('break_end')->nullable();
            $table->string('status')->default('承認待ち');
            $table->timestamps();

            // 外部キー
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('attendance_id')->references('id')->on('attendances')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_requests');
    }
}