<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceStatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 現在の日時が画面に表示される()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        // Blade の表示に合わせる
        $date = now()->format('Y年m月d日');
        $time = now()->format('H:i');

        $response->assertSee($date);
        $response->assertSee($time);
    }

    /** @test */
    public function 勤務外ステータスが正しく表示される()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('勤務外');
    }

    /** @test */
    public function 出勤中ステータスが正しく表示される()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
            'status' => '出勤中',
            'start_time' => now(),
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('出勤中');
    }

    /** @test */
    public function 休憩中ステータスが正しく表示される()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
            'status' => '休憩中',
            'start_time' => now()->subHours(2),
        ]);

        BreakRecord::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => now()->subHour(),
            'break_end' => null,
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('休憩中');
    }

    /** @test */
    public function 退勤済ステータスが正しく表示される()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
            'status' => '退勤済',
            'start_time' => now()->subHours(8),
            'end_time' => now()->subHour(),
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('退勤済');
    }
}