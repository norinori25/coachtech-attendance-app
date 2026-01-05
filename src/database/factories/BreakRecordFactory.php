<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;

class BreakRecordFactory extends Factory
{
    public function definition()
    {
        $start = $this->faker->dateTimeBetween('-3 hours', '-1 hours');
        $end = $this->faker->dateTimeBetween($start, 'now');

        return [
            'attendance_id' => Attendance::factory(),
            'break_start' => $start,
            'break_end' => $end,
        ];
    }
}