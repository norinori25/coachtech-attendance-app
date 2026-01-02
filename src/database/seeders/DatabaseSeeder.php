<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakRecord;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 管理者ユーザーを1人作成
        User::factory()->create([
            'name' => '管理者ユーザー',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'is_admin' => true, // 管理者フラグ
        ]);

        // 一般ユーザーを10人作成
        User::factory(10)->create();

        // 勤怠データを50件作成（既存ユーザーに紐付け）
        Attendance::factory(50)->create();

        // 休憩レコードを100件作成（勤怠データに紐付け）
        BreakRecord::factory(100)->create();
    }
}