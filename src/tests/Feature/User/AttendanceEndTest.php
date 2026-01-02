<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\CreatesAttendanceStates;
use App\Models\Attendance;

class AttendanceEndTest extends TestCase
{
    use RefreshDatabase, CreatesAttendanceStates;

    /** @test */
    public function 退勤ボタンが正しく機能する()
    {
        $user = $this->createStarted($this->createUser());

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('退勤');

        $this->actingAs($user)->post('/attendance', ['action' => 'end']);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('退勤済');
    }

    /** @test */
    public function 退勤時刻が勤怠一覧画面で確認できる()
    {
        $user = $this->createUser();

        $this->actingAs($user)->post('/attendance', ['action' => 'start']);
        $this->actingAs($user)->post('/attendance', ['action' => 'end']);

        $attendance = Attendance::where('user_id', $user->id)
        ->where('date', now()->toDateString())
        ->firstOrFail();

        $end = $attendance->end_time->format('H:i');

        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertSee($end);
    }
}