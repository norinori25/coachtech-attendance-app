<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\BreakRecord;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
public function 勤怠詳細画面に正しい情報が表示される()
{
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'is_admin' => false,
        'name' => 'テスト太郎',
    ]);

    $attendance = Attendance::factory()->create([
        'user_id' => $user->id,
        'date' => '2024-01-15',
        'start_time' => '09:00:00',
        'end_time' => '18:00:00',
    ]);

    BreakRecord::factory()->create([
        'attendance_id' => $attendance->id,
        'break_start' => '12:00:00',
        'break_end' => '13:00:00',
    ]);

    $this->actingAs($user);

    $response = $this->get('/attendance/detail/' . $attendance->id);

    $response->assertStatus(200);

    // 名前
    $response->assertSee('テスト太郎');

    // 日付
    $response->assertSee('2024年');
    $response->assertSee('01月15日');

    // 出勤・退勤
    $response->assertSee('09:00');
    $response->assertSee('18:00');

    // 休憩（開始・終了）
    $response->assertSee('12:00');
    $response->assertSee('13:00');
}
}