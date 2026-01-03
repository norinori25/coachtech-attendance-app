<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\AttendanceRequest;

class AttendanceRequestFactory extends Factory
{
    protected $model = AttendanceRequest::class;

    public function definition()
    {
        return [
            'user_id'            => null,
            'attendance_id'      => null,
            'attendance_date'    => now()->toDateString(),
            'reason'             => $this->faker->sentence(),
            'break_start'        => '12:00',
            'break_end'          => '13:00',
            'status'             => '承認待ち',
        ];
    }

    /**
     * 時刻付きの修正申請
     */
    public function withTimes()
    {
        return $this->state(function (array $attributes) {
            return [
                'request_start_time' => '09:00:00',
                'request_end_time'   => '18:00:00',
            ];
        });
    }
}