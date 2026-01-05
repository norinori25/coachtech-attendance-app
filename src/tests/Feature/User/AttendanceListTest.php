<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
public function 自分の勤怠情報が一覧に全て表示される()
{
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'is_admin' => false,
    ]);

    $attendances = Attendance::factory()->count(3)->create([
        'user_id' => $user->id,
    ]);

    $this->actingAs($user);

    $month = Carbon::parse($attendances[0]->date)->format('Y-m');
    $response = $this->get('/attendance/list?month=' . $month);

    foreach ($attendances as $attendance) {

        // この勤怠が表示対象の月か？
        if (Carbon::parse($attendance->date)->format('Y-m') !== $month) {
            continue; // 表示されないのでスキップ
        }

        $date = Carbon::parse($attendance->date);
        $week = ['日','月','火','水','木','金','土'];
        $w = $week[$date->dayOfWeek];

        $expected = $date->format('m/d') . "($w)";

        $response->assertSee($expected);
    }
}

    /** @test */
    public function 勤怠一覧画面で現在の月が表示される()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance/list');

        $response->assertStatus(200);

        $response->assertSee(now()->format('Y/m'));
    }

    /** @test */
    public function 前月ボタンで前月の勤怠情報が表示される()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $this->actingAs($user);

        $prevMonth = now()->subMonth()->format('Y-m');

        $response = $this->get('/attendance/list?month=' . $prevMonth);

        $response->assertStatus(200);

        $response->assertSee(now()->subMonth()->format('Y/m'));
    }

    /** @test */
    public function 翌月ボタンで翌月の勤怠情報が表示される()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $this->actingAs($user);

        $nextMonth = now()->addMonth()->format('Y-m');

        $response = $this->get('/attendance/list?month=' . $nextMonth);

        $response->assertStatus(200);

        $response->assertSee(now()->addMonth()->format('Y/m'));
    }

    /** @test */
    public function 詳細ボタンから勤怠詳細画面に遷移できる()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        'date' => now()->format('Y-m-d'),
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance/list');

        $response->assertStatus(200);

        // HTML エスケープを無視して検索
        $response->assertSee('/attendance/detail/' . $attendance->id);
    }
}