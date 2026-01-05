<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminStaffTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin()
    {
        $admin = User::factory()->create([
            'is_admin' => 1,
            'email_verified_at' => now(),
        ]);

        return $this->actingAs($admin, 'web');
    }

    /** @test */
    public function 管理者は全ユーザーの氏名とメールアドレスを確認できる()
    {
        $this->actingAsAdmin();

        $users = User::factory()->count(3)->create();

        $response = $this->get('/admin/staff/list');

        $response->assertStatus(200);

        foreach ($users as $user) {
            $response->assertSee($user->name);
            $response->assertSee($user->email);
        }
    }

    /** @test */
    public function 管理者は選択したユーザーの勤怠一覧を確認できる()
    {
        $this->actingAsAdmin();

        $user = User::factory()->create();

        $attendances = Attendance::factory()->count(3)->create([
        'user_id' => $user->id,
        ]);

        $response = $this->get('/admin/attendance/staff/' . $user->id);

        $response->assertStatus(200);

        foreach ($attendances as $attendance) {

            $date = Carbon::parse($attendance->date);

            $week = ['日','月','火','水','木','金','土'];
            $w = $week[$date->dayOfWeek];

            $formatted = $date->format('m/d') . "($w)";

            $response->assertSee($formatted);
        }
    }

    /** @test */
    public function 前月ボタンで前月の勤怠が表示される()
    {
        $this->actingAsAdmin();

        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-12-10',
        ]);

        $prev = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-11-10',
        ]);

        $response = $this->get('/admin/attendance/staff/' . $user->id . '?month=2025-11');

        $response->assertStatus(200);

        $date = Carbon::parse($prev->date);
        $week = ['日','月','火','水','木','金','土'];
        $formatted = $date->format('m/d') . '(' . $week[$date->dayOfWeek] . ')';

        $response->assertSee($formatted);
        $response->assertDontSee('12/10');
    }

    /** @test */
    public function 翌月ボタンで翌月の勤怠が表示される()
    {
        $this->actingAsAdmin();

        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-12-10',
        ]);

        $next = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2026-01-10',
        ]);

        $response = $this->get('/admin/attendance/staff/' . $user->id . '?month=2026-01');

        $response->assertStatus(200);

        $date = Carbon::parse($next->date);
        $week = ['日','月','火','水','木','金','土'];
        $formatted = $date->format('m/d') . '(' . $week[$date->dayOfWeek] . ')';

        $response->assertSee($formatted);
        $response->assertDontSee('12/10');
    }

    /** @test */
    public function 詳細ボタンから勤怠詳細画面に遷移できる()
    {
        $this->actingAsAdmin();

        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        'date' => now()->format('Y-m-d'),
        ]);

        $response = $this->get('/admin/attendance/staff/' . $user->id);

        $response->assertStatus(200);

        $response->assertSee('/admin/attendance/' . $attendance->id);
    }
}