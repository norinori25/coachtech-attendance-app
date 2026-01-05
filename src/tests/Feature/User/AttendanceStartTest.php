<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\CreatesAttendanceStates;
use App\Models\Attendance;

class AttendanceStartTest extends TestCase
{
    use RefreshDatabase, CreatesAttendanceStates;

    /** @test */
    public function 出勤ボタンが正しく機能する()
    {
        // 1. 勤務外ユーザーを作成
        $user = $this->createUser();

        // 2. 出勤ボタンが表示されることを確認
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('出勤');

        // 3. 出勤処理を実行
        $response = $this->actingAs($user)->post('/attendance', [
            'action' => 'start',
        ]);

        // 正しくリダイレクトされること
        $response->assertRedirect('/attendance');

        // DB に出勤記録が作成されていること
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => '出勤中',
        ]);

        // 画面上のステータスが「出勤中」になる
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('出勤中');
    }

    /** @test */
    public function 出勤は一日一回のみできる()
    {
        // 1. 退勤済ユーザーを作成
        $user = $this->createUser();
        $this->createFinished($user);

        // 2. 出勤ボタンが表示されないことを確認
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertDontSee('value="start"', false);
    }

    /** @test */
    public function 出勤時刻が勤怠一覧画面で確認できる()
    {
        // 1. 勤務外ユーザーを作成
        $user = $this->createUser();

        // 2. 出勤処理を実行
        $this->actingAs($user)->post('/attendance', [
            'action' => 'start',
        ]);

        // 出勤時刻を取得（DBから）
        $attendance = Attendance::where('user_id', $user->id)->first();
        $start = $attendance->start_time->format('H:i');

        // 3. 勤怠一覧画面で出勤時刻が表示されていることを確認
        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertSee($start);
    }
}