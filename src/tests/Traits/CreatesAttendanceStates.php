<?php

namespace Tests\Traits;

use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakRecord;

trait CreatesAttendanceStates
{
    protected function createUser()
    {
        return User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    protected function createStarted($user)
    {
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
            'status' => '出勤中',
            'start_time' => now(),
        ]);

        return $user;
    }

    protected function createResting($user)
    {
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
            'status' => '休憩中',
            'start_time' => now()->subHours(2),
        ]);

        BreakRecord::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => now()->subHours(1),
            'break_end' => null,
        ]);

        return $attendance;
    }   

    protected function createFinished($user)
    {
        return Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
            'status' => '退勤済',
            'start_time' => now()->subHours(8),
            'end_time' => now()->subHours(1),
        ]);
    }
}