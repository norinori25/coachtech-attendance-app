@extends('layouts.default')

@section('title', '勤怠詳細（管理者用）')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance_detail.css') }}">
@endsection

@section('content')

<div class="container">

    <h1 class="attendance-title">
        <span class="attendance-title__line"></span>
        勤怠詳細
    </h1>

    {{-- 申請が存在する場合（承認画面） --}}
    @if($attendanceRequest)

        <table class="table attendance-detail-table">
            <tr>
                <th>名前</th>
                <td>{{ $attendanceRequest->user->name }}</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <th>日付</th>
                <td class="year-cell">{{ optional($attendanceRequest->attendance_date)->format('Y年') }}</td>
                <td></td>
                <td class="monthday-cell">{{ optional($attendanceRequest->attendance_date)->format('m月d日') }}</td>
                <td></td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td>{{ optional($attendanceRequest->attendance->start_time)->format('H:i') }}</td>
                <td class="time-separator">～</td>
                <td>{{ optional($attendanceRequest->attendance->end_time)->format('H:i') }}</td>
                <td></td>
            </tr>

            @foreach($attendanceRequest->attendance->breakRecords as $index => $break)
                <tr>
                    <th>休憩{{ $index + 1 }}</th>
                    <td>{{ optional($break->break_start)->format('H:i') }}</td>
                    <td class="time-separator">～</td>
                    <td>{{ optional($break->break_end)->format('H:i') }}</td>
                    <td></td>
                </tr>
            @endforeach

            <tr>
                <th>備考</th>
                <td colspan="3">{{ $attendanceRequest->attendance->note ?? '' }}</td>
                <td></td>
            </tr>
        </table>

        {{-- 管理者用承認ボタン --}}
        @if($attendanceRequest->status === '承認待ち')
            <form action="{{ route('admin.attendance_request.approve', $attendanceRequest->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-dark">承認</button>
            </form>
        @else
            <button type="button" class="btn btn-secondary" disabled>承認済み</button>
        @endif

    {{-- 申請が存在しない場合（管理者が直接修正） --}}
    @else

        <form action="{{ route('admin.attendance.update', $attendance->id) }}" method="POST">
            @csrf

            <table class="table attendance-detail-table">
                <tr>
                    <th>名前</th>
                    <td>{{ $attendance->user->name }}</td>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <th>日付</th>
                    <td class="year-cell">{{ $attendance->date->format('Y年') }}</td>
                    <td></td>
                    <td class="monthday-cell">{{ $attendance->date->format('m月d日') }}</td>
                    <td></td>
                </tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        <input type="time" name="start_time" value="{{ old('start_time', optional($attendance->start_time)->format('H:i')) }}">
                        @error('start_time') <div class="form__error">{{ $message }}</div> @enderror
                    </td>
                    <td class="time-separator">～</td>
                    <td>
                        <input type="time" name="end_time" value="{{ old('end_time', optional($attendance->end_time)->format('H:i')) }}">
                        @error('end_time') <div class="form__error">{{ $message }}</div> @enderror
                    </td>
                    <td></td>
                </tr>

                @php
                    $firstBreak = $attendance->breakRecords->first();
                @endphp

                <tr>
                    <th>休憩</th>
                    <td>
                        <input type="time" name="break_start_time" value="{{ old('break_start_time', optional($firstBreak?->break_start)->format('H:i')) }}">
                    </td>
                    <td class="time-separator">～</td>
                    <td>
                        <input type="time" name="break_end_time" value="{{ old('break_end_time', optional($firstBreak?->break_end)->format('H:i')) }}">
                    </td>
                    <td></td>
                </tr>

                <tr>
                    <th>備考</th>
                    <td colspan="3">
                        <textarea name="note" class="note-field" required>{{ old('note', $attendance->note) }}</textarea>
                        @error('note') <div class="form__error">{{ $message }}</div> @enderror
                    </td>
                    <td></td>
                </tr>
            </table>

            <div class="text-end mt-2">
                <button type="submit" class="btn btn-dark">修正</button>
            </div>
        </form>

    @endif
</div>

@endsection