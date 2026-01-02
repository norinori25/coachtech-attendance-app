<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;

class BreakRecordFactory extends Factory
{
    public function definition()
    {
        return [
            'attendance_id' => Attendance::inRandomOrder()->first()->id ?? Attendance::factory(),
            'break_start' => $this->faker->dateTime(),
            'break_end' => $this->faker->dateTime(),
        ];
    }
}