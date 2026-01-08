<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceRequest;

class AttendanceDetailUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 出勤時間が退勤時間より後ならエラーになる()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('attendance_request.store'), [
            'attendance_id' => $attendance->id,
            'start_time_new' => '18:00',
            'end_time_new' => '09:00',
            'break_start_new' => '10:00',
            'break_end_new' => '11:00',
            'note' => 'test',
        ]);

        $response->assertSessionHasErrors([
            'start_time_new' => '出勤時間もしくは退勤時間が不適切な値です',
        ]);
    }

    /** @test */
    public function 休憩開始時間が退勤時間より後ならエラーになる()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('attendance_request.store'), [
            'attendance_id' => $attendance->id,
            'start_time_new' => '09:00',
            'end_time_new' => '18:00',
            'breaks' => [
                ['start' => '19:00', 'end' => '20:00']
            ],
            'note' => 'test',
        ]);

        $response->assertSessionHasErrors([
            'breaks.0.start' => '休憩時間が不適切な値です',
        ]);
    }

    /** @test */
    public function 休憩終了時間が退勤時間より後ならエラーになる()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('attendance_request.store'), [
            'attendance_id' => $attendance->id,
            'start_time_new' => '09:00',
            'end_time_new' => '18:00',
            'break_start_new' => '12:00',
            'break_end_new' => '19:00',
            'note' => 'test',
        ]);

        $response->assertSessionHasErrors([
            'break_end_new' => '休憩時間もしくは退勤時間が不適切な値です',
        ]);
    }

    /** @test */
    public function 備考欄が未入力ならエラーになる()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('attendance_request.store'), [
            'attendance_id' => $attendance->id,
            'start_time_new' => '09:00',
            'end_time_new' => '18:00',
            'break_start_new' => '12:00',
            'break_end_new' => '13:00',
            'note' => '',
        ]);

        $response->assertSessionHasErrors([
            'note' => '備考を記入してください',
        ]);
    }

    /** @test */
    public function 修正申請が作成される()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)->post(route('attendance_request.store'), [
            'attendance_id' => $attendance->id,
            'start_time_new' => '09:00',
            'end_time_new' => '18:00',
            'break_start_new' => '12:00',
            'break_end_new' => '13:00',
            'note' => '修正理由',
        ]);

        $this->assertDatabaseHas('attendance_requests', [
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'status' => '承認待ち',
        ]);
    }

    /** @test */
public function 承認待ち一覧に自分の申請が表示される()
{
    $user = User::factory()->create([
        'email_verified_at' => now(), // メール認証済みにする
    ]);
    
    $attendance = Attendance::factory()->create([
        'user_id' => $user->id,
    ]);

    $request = AttendanceRequest::factory()->create([
        'user_id' => $user->id,
        'attendance_id' => $attendance->id,
        'status' => '承認待ち',
    ]);

    $response = $this->actingAs($user)->get(route('attendance_request.index', ['status' => 'pending']));

    $response->assertSee($request->reason);
}

/** @test */
public function 承認済みに管理者が承認した申請が表示される()
{
    $user = User::factory()->create([
        'email_verified_at' => now(), // メール認証済みにする
    ]);
    
    $attendance = Attendance::factory()->create([
        'user_id' => $user->id,
    ]);

    $request = AttendanceRequest::factory()->create([
        'user_id' => $user->id,
        'attendance_id' => $attendance->id,
        'status' => '承認済み',
    ]);

    $response = $this->actingAs($user)->get(route('attendance_request.index', ['status' => 'approved']));

    $response->assertSee($request->reason);
}

    /** @test */
public function 申請詳細画面に遷移できる()
{
    $user = User::factory()->create();
    $attendance = Attendance::factory()->create([
        'user_id' => $user->id,
    ]);

    $request = AttendanceRequest::factory()->create([
        'user_id' => $user->id,
        'attendance_id' => $attendance->id,
        'status' => '承認待ち',
    ]);

    $response = $this->actingAs($user)->get(route('attendance_request.show', $request->id));

    $response->assertStatus(200);
    
    $response->assertSee($request->attendance_date->format('Y年'));
    $response->assertSee($request->attendance_date->format('m月d日'));
}
}