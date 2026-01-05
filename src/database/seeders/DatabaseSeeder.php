<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakRecord;
use App\Models\AttendanceRequest;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 管理者ユーザー
        User::factory()->create([
            'name' => '管理者ユーザー',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'is_admin' => true,
        ]);

        // 一般ユーザー10人
        $users = User::factory(10)->create();

        // 各ユーザーに勤怠データを作成
        foreach ($users as $user) {

            // 勤務外
            Attendance::factory(5)->create([
                'user_id' => $user->id,
            ]);

            // 出勤中
            Attendance::factory()->started()->create([
                'user_id' => $user->id,
            ]);

            // 休憩中（休憩レコード付き）
            $resting = Attendance::factory()->resting()->create([
                'user_id' => $user->id,
            ]);
            BreakRecord::factory()->create([
                'attendance_id' => $resting->id,
            ]);

            // 退勤済（休憩レコード付き）
            $finished = Attendance::factory()->finished()->create([
                'user_id' => $user->id,
            ]);
            BreakRecord::factory()->create([
                'attendance_id' => $finished->id,
            ]);

            // 申請データ
            AttendanceRequest::factory(2)->create([
                'user_id' => $user->id,
                'attendance_id' => $finished->id,
            ]);
        }
    }
}