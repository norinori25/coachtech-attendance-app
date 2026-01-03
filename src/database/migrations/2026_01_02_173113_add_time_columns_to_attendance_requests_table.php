<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimeColumnsToAttendanceRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('attendance_requests', function (Blueprint $table) {
            $table->time('request_start_time')->nullable()->after('attendance_id');
            $table->time('request_end_time')->nullable()->after('request_start_time');
            // attendance_date は既に存在するので削除
        });
    }

    public function down()
    {
        Schema::table('attendance_requests', function (Blueprint $table) {
            $table->dropColumn(['request_start_time', 'request_end_time']);
        });
    }
}