<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 管理者は勤怠詳細画面に正しい情報を表示できる()
    {
        $this->withoutExceptionHandling();

        $admin = User::factory()->create([
            'is_admin' => 1,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin);

        $attendance = Attendance::factory()->create([
            'date'        => '2024-01-15',
            'start_time'  => '09:00:00',
            'end_time'    => '18:00:00',
        ]);

        BreakRecord::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start'   => '12:00:00',
            'break_end'     => '13:00:00',
        ]);

        $response = $this->get('/admin/attendance/' . $attendance->id);

        $response->assertStatus(200);

        // ユーザー名
        $response->assertSee($attendance->user->name);

        // 日付
        $response->assertSee('2024年');
        $response->assertSee('01月15日');

        // 出勤・退勤
        $response->assertSee('09:00');
        $response->assertSee('18:00');

        // 休憩
        $response->assertSee('12:00');
        $response->assertSee('13:00');
    }

    /** @test */
    public function 出勤時間が退勤時間より後の場合エラーになる()
    {
        $admin = User::factory()->create([
            'is_admin' => 1,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin);

        $attendance = Attendance::factory()->create();

        $response = $this->post('/admin/attendance/' . $attendance->id, [
            'start_time'        => '20:00',
            'end_time'          => '10:00',
            'break_start_time'  => '12:00',
            'break_end_time'    => '13:00',
            'note'              => 'テスト',
        ]);

        $response->assertSessionHasErrors([
            'start_time' => '出勤時間もしくは退勤時間が不適切な値です',
        ]);
    }

    /** @test */
    public function 休憩開始時間が退勤時間より後の場合エラーになる()
    {
        $admin = User::factory()->create([
            'is_admin' => 1,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin);

        $attendance = Attendance::factory()->create();

        $response = $this->post('/admin/attendance/' . $attendance->id, [
            'start_time'        => '09:00',
            'end_time'          => '18:00',
            'break_start_time'  => '20:00',
            'break_end_time'    => '21:00',
            'note'              => 'テスト',
        ]);

        $response->assertSessionHasErrors([
            'break_start_time' => '休憩時間が不適切な値です',
        ]);
    }

    /** @test */
    public function 備考が未入力の場合エラーになる()
    {
        $admin = User::factory()->create([
            'is_admin' => 1,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin);

        $attendance = Attendance::factory()->create();

        $response = $this->post('/admin/attendance/' . $attendance->id, [
            'start_time'        => '09:00',
            'end_time'          => '18:00',
            'break_start_time'  => '12:00',
            'break_end_time'    => '13:00',
            'note'              => '',
        ]);

        $response->assertSessionHasErrors([
            'note' => '備考を記入してください',
        ]);
    }
}