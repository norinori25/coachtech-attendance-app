<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin()
    {
        // 管理者ユーザー（webガードでログイン）
        $admin = User::factory()->create([
            'is_admin' => 1,
        ]);

        return $this->actingAs($admin, 'web');
    }

    /** @test */
    public function 管理者は当日の全ユーザーの勤怠情報を確認できる()
    {
        $this->withoutExceptionHandling();

        $this->actingAsAdmin();

        // 今日の日付
        $today = Carbon::today();

        // ユーザーと勤怠データを作成
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user1->id,
            'date' => $today->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        Attendance::factory()->create([
            'user_id' => $user2->id,
            'date' => $today->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '19:00:00',
        ]);

        $response = $this->get('/admin/attendance/list');

        $response->assertStatus(200);

        // 名前が表示されているか
        $response->assertSee($user1->name);
        $response->assertSee($user2->name);

        // 出勤・退勤時刻が表示されているか
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('10:00');
        $response->assertSee('19:00');
    }

    /** @test */
    public function 勤怠一覧画面に今日の日付が表示される()
    {
    $this->withoutExceptionHandling();
    $this->actingAsAdmin();

    $today = Carbon::today()->format('Y/m/d');

    $response = $this->get('/admin/attendance/list');

    $response->assertStatus(200);
    $response->assertSee($today);
    }

    /** @test */
    public function 前日ボタンで前日の勤怠情報が表示される()
    {
    $this->withoutExceptionHandling();
    $this->actingAsAdmin();

    $yesterday = Carbon::yesterday()->toDateString();

    // 前日の勤怠データを作成
    $user = User::factory()->create();
    Attendance::factory()->create([
        'user_id' => $user->id,
        'date' => $yesterday,
        'start_time' => '09:00:00',
        'end_time' => '18:00:00',
    ]);

    // 前日パラメータでアクセス
    $response = $this->get('/admin/attendance/list?date=' . $yesterday);

    $response->assertStatus(200);
    $response->assertSee('09:00');
    $response->assertSee('18:00');
    }

    /** @test */
    public function 翌日ボタンで翌日の勤怠情報が表示される()
    {
    $this->withoutExceptionHandling();
    $this->actingAsAdmin();

    $tomorrow = Carbon::tomorrow()->toDateString();

    // 翌日の勤怠データを作成
    $user = User::factory()->create();
    Attendance::factory()->create([
        'user_id' => $user->id,
        'date' => $tomorrow,
        'start_time' => '10:00:00',
        'end_time' => '19:00:00',
    ]);

    // 翌日パラメータでアクセス
    $response = $this->get('/admin/attendance/list?date=' . $tomorrow);

    $response->assertStatus(200);
    $response->assertSee('10:00');
    $response->assertSee('19:00');
    }
}