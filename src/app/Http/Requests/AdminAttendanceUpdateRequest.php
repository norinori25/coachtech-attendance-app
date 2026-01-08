<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class AdminAttendanceUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'start_time'        => ['required', 'date_format:H:i'],
            'end_time'          => ['required', 'date_format:H:i'],
            'break_start_time'  => ['nullable', 'date_format:H:i'],
            'break_end_time'    => ['nullable', 'date_format:H:i'],
            'note'              => ['required'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $start = $this->start_time;
            $end   = $this->end_time;
            $breakStart = $this->break_start_time;
            $breakEnd   = $this->break_end_time;

            // 出勤 > 退勤
            if ($start && $end && Carbon::parse($start)->gt(Carbon::parse($end))) {
                $validator->errors()->add(
                    'start_time',
                    '出勤時間もしくは退勤時間が不適切な値です'
                );
            }

            // 休憩開始 < 出勤 OR > 退勤
            if (
                $breakStart &&
                (
                    ($start && Carbon::parse($breakStart)->lt(Carbon::parse($start))) ||
                    ($end   && Carbon::parse($breakStart)->gt(Carbon::parse($end)))
                )
            ) {
                $validator->errors()->add(
                    'break_start_time',
                    '休憩時間が不適切な値です'
                );
            }

            // 休憩終了 > 退勤
            if ($breakEnd && $end && Carbon::parse($breakEnd)->gt(Carbon::parse($end))) {
                $validator->errors()->add(
                    'break_end_time',
                    '休憩時間もしくは退勤時間が不適切な値です'
                );
            }
        });
    }

    public function messages()
    {
        return [
            'note.required' => '備考を記入してください',
        ];
    }
}
