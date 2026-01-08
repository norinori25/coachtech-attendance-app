<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class AttendanceCorrectionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'start_time_new'  => ['nullable', 'date_format:H:i'],
            'end_time_new'    => ['nullable', 'date_format:H:i'],
            'breaks.*.start'  => ['nullable', 'date_format:H:i'],
            'breaks.*.end'    => ['nullable', 'date_format:H:i'],
            'note'            => ['required', 'string'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $start = $this->input('start_time_new');
            $end   = $this->input('end_time_new');
            $breaks = $this->input('breaks', []);

            // ★ 追加：単発休憩（テスト用）
            $breakStartNew = $this->input('break_start_new');
            $breakEndNew   = $this->input('break_end_new');

            // 出勤 > 退勤
            if ($start && $end && Carbon::parse($start)->gt(Carbon::parse($end))) {
                $validator->errors()->add(
                    'start_time_new',
                    '出勤時間もしくは退勤時間が不適切な値です'
                );
            }

            // ★ 単発休憩チェック（foreach の外）
            if ($breakStartNew && $end && Carbon::parse($breakStartNew)->gt(Carbon::parse($end))) {
                $validator->errors()->add(
                    'break_start_new',
                    '休憩時間が不適切な値です'
                );
            }

            if ($breakEndNew && $end && Carbon::parse($breakEndNew)->gt(Carbon::parse($end))) {
                $validator->errors()->add(
                    'break_end_new',
                    '休憩時間もしくは退勤時間が不適切な値です'
                );
            }

            // ★ 複数休憩（画面用）
            foreach ($breaks as $index => $break) {
                $breakStart = $break['start'] ?? null;
                $breakEnd   = $break['end'] ?? null;

                if ($breakStart && $end && Carbon::parse($breakStart)->gt(Carbon::parse($end))) {
                    $validator->errors()->add(
                        "breaks.$index.start",
                        '休憩時間が不適切な値です'
                    );
                }

                if ($breakEnd && $end && Carbon::parse($breakEnd)->gt(Carbon::parse($end))) {
                    $validator->errors()->add(
                        "breaks.$index.end",
                        '休憩時間もしくは退勤時間が不適切な値です'
                    );
                }
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