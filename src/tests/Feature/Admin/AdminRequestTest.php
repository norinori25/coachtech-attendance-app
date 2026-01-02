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
        $response->assertDontSee($approved->id);
    }
}