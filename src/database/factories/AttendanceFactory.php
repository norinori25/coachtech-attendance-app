<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => null,
            'date' => now()->format('Y-m-d'),
            'start_time' => null,
            'end_time' => null,
            'status' => '勤務外',
        ];
    }
}