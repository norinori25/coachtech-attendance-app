<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceRequest;

class AdminRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 承認待ちの修正申請が全て表示される()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $attendance = Attendance::factory()->create([
            'user_id' => $admin->id,
        ]);

        $pending1 = AttendanceRequest::factory()->create([
            'user_id' => $admin->id,
            'attendance_id' => $attendance->id,
            'status' => '承認待ち',
        ]);

        $pending2 = AttendanceRequest::factory()->create([
            'user_id' => $admin->id,
            'attendance_id' => $attendance->id,
            'status' => '承認待ち',
        ]);

        $approved = AttendanceRequest::factory()->create([
            'user_id' => $admin->id,
            'attendance_id' => $attendance->id,
            'status' => '承認済み',
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/stamp_correction_request/list?status=pending');

        $response->assertSee($pending1->id);
        $response->assertSee($pending2->id);
        $response->assertDontSee($approved->reason);
    }

    /** @test */
    public function 承認済みの修正申請が全て表示される()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $attendance1 = Attendance::factory()->create([
            'user_id' => $user1->id,
        ]);

        $attendance2 = Attendance::factory()->create([
            'user_id' => $user2->id,
        ]);

        $approved1 = AttendanceRequest::factory()->create([
            'user_id' => $user1->id,
            'attendance_id' => $attendance1->id,
            'status' => '承認済み',
        ]);

        $approved2 = AttendanceRequest::factory()->create([
            'user_id' => $user2->id,
            'attendance_id' => $attendance2->id,
            'status' => '承認済み',
        ]);

        $pending = AttendanceRequest::factory()->create([
            'user_id' => $user1->id,
            'attendance_id' => $attendance1->id,
            'status' => '承認待ち',
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/stamp_correction_request/list?status=approved');

        $response->assertStatus(200);

        $response->assertSee($approved1->id);
        $response->assertSee($approved2->id);

        $response->assertDontSee($pending->reason);
    }

   /** @test */
    public function 修正申請の詳細内容が正しく表示されている()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['name' => '山田太郎']);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2026-01-15',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);
        $request = AttendanceRequest::factory()->withTimes()->create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'attendance_date' => '2026-01-15',
            'request_start_time' => '09:30:00',
            'request_end_time' => '18:30:00',
            'status' => '承認待ち',
            'reason' => '電車遅延のため',
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/stamp_correction_request/approve/' . $request->id);

        $response->assertStatus(200);

        $response->assertSee('山田太郎');
        $response->assertSee('2026年');
        $response->assertSee('01月15日');
        $response->assertSee('電車遅延のため');
    }

    /** @test */
    public function 修正申請の承認処理が正しく行われる()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['name' => '山田太郎']);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2026-01-15',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        $request = AttendanceRequest::factory()->withTimes()->create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'attendance_date' => '2026-01-15',
            'request_start_time' => '09:30:00',
            'request_end_time' => '18:30:00',
            'status' => '承認待ち',
            'reason' => '電車遅延のため',
        ]);

        $response = $this->actingAs($admin)
            ->post('/admin/stamp_correction_request/approve/' . $request->id);

        $response->assertRedirect('/admin/stamp_correction_request/list');

        $this->assertDatabaseHas('attendance_requests', [
            'id' => $request->id,
            'status' => '承認済み',
        ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'start_time' => '09:30:00',
            'end_time' => '18:30:00',
        ]);
    }
}