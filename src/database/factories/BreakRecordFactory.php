<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;

class BreakRecordFactory extends Factory
{
    public function definition()
    {
        return [
            'attendance_id' => Attendance::factory(),
            'break_start' => $this->faker->dateTimeBetween('-3 hours', '-1 hours'),
            'break_end' => $this->faker->dateTimeBetween('-1 hours', 'now'),
        ];
    }
}