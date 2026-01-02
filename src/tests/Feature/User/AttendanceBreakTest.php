<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\CreatesAttendanceStates;
use App\Models\Attendance;
use App\Models\BreakRecord;

class AttendanceBreakTest extends TestCase
{
    use RefreshDatabase, CreatesAttendanceStates;

    /** @test */
    public function 休憩ボタンが正しく機能する()
    {
        // 出勤中ユーザーを作成
        $user = $this->createStarted($user = $this->createUser());

        // 休憩入ボタンが表示される
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('休憩入');

        // 休憩入処理
        $this->actingAs($user)->post('/attendance', [
            'action' => 'break_in',
        ]);

        // ステータスが休憩中に変わる
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('休憩中');
    }

    /** @test */
    public function 休憩は一日に何回でもできる()
    {
        $user = $this->createStarted($this->createUser());

        // 1回目の休憩入 → 休憩戻
        $this->actingAs($user)->post('/attendance', ['action' => 'break_in']);
        $this->actingAs($user)->post('/attendance', ['action' => 'break_out']);

        // 再度休憩入ボタンが表示される
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('休憩入');
    }

    /** @test */
    public function 休憩戻ボタンが正しく機能する()
    {
        $user = $this->createStarted($this->createUser());

        // 休憩入
        $this->actingAs($user)->post('/attendance', ['action' => 'break_in']);

        // 休憩戻
        $this->actingAs($user)->post('/attendance', ['action' => 'break_out']);

        // ステータスが出勤中に戻る
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('出勤中');
    }

    /** @test */
    public function 休憩戻は一日に何回でもできる()
    {
        $user = $this->createStarted($this->createUser());

        // 1回目の休憩入 → 休憩戻
        $this->actingAs($user)->post('/attendance', ['action' => 'break_in']);
        $this->actingAs($user)->post('/attendance', ['action' => 'break_out']);

        // 2回目の休憩入
        $this->actingAs($user)->post('/attendance', ['action' => 'break_in']);

        // 休憩戻ボタンが表示される
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('休憩戻');
    }

    /** @test */
    public function 休憩時刻が勤怠一覧画面で確認できる()
    {
        $user = $this->createStarted($this->createUser());

        // 休憩入
        $this->actingAs($user)->post('/attendance', ['action' => 'break_in']);
        $break = BreakRecord::first();
        $breakStart = $break->break_start->format('H:i');

        // 休憩戻
        $this->actingAs($user)->post('/attendance', ['action' => 'break_out']);
        $break->refresh();
        $breakEnd = $break->break_end->format('H:i');

        // 勤怠一覧画面に休憩時刻が表示される
        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertSee($breakStart);
        $response->assertSee($breakEnd);
    }
}