<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class AttendanceFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'date' => now()->format('Y-m-d'),
            'start_time' => null,
            'end_time' => null,
            'status' => '勤務外',
        ];
    }

    // 出勤中
    public function started()
    {
        return $this->state(function () {
            return [
                'status' => '出勤中',
                'start_time' => now()->subHours(2),
            ];
        });
    }

    // 休憩中
    public function resting()
    {
        return $this->state(function () {
            return [
                'status' => '休憩中',
                'start_time' => now()->subHours(3),
            ];
        });
    }

    // 退勤済
    public function finished()
    {
        return $this->state(function () {
            return [
                'status' => '退勤済',
                'start_time' => now()->subHours(8),
                'end_time' => now()->subHours(1),
            ];
        });
    }
}