@extends('layouts.default')

@section('title', '勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance.css')  }}">
@endsection

@section('content')

<div class="container">
    {{-- タイトル --}}
    <h1 class="attendance-title">
        <span class="attendance-title__line"></span>
        勤怠一覧
    </h1>

    {{-- カレンダーバー --}}
    <div class="calendar-bar">
        <div class="calendar-bar__prev">
            <a href="{{ route('attendance.index', ['month' => $currentMonth->copy()->subMonth()->format('Y-m')]) }}">← 前月
            </a>
        </div>
        <div class="calendar-bar__current">
            <span class="calendar-icon">📅</span>
            {{ $currentMonth->format('Y/m') }}
        </div>
        <div class="calendar-bar__next">
            <a href="{{ route('attendance.index', ['month' => $currentMonth->copy()->addMonth()->format('Y-m')]) }}">
                翌月 →
            </a>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $attendance)
                <tr>
                    {{-- 日付を日本語曜日付きで表示 --}}
                    <td>{{ \Carbon\Carbon::parse($attendance->date)->locale('ja')->isoFormat('MM/DD(dd)') }}</td>

                    {{-- 出勤・退勤時間を H:i 形式で表示 --}}
                    <td>{{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '' }}</td>
                    <td>{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}</td>

                    {{-- 休憩時間を hh:mm 形式で表示 --}}
                    <td>
                        @php
                            $totalBreakMinutes = 0;
                            foreach ($attendance->breakRecords as $break) {
                                if ($break->start_time && $break->end_time) {
                                    $totalBreakMinutes += \Carbon\Carbon::parse($break->end_time)
                                        ->diffInMinutes(\Carbon\Carbon::parse($break->start_time));
                                }
                            }
                        @endphp
                        {{ sprintf('%d:%02d', floor($totalBreakMinutes / 60), $totalBreakMinutes % 60) }}
                    </td>

                    {{-- 合計勤務時間（モデルのアクセサ利用） --}}
                    <td>{{ $attendance->total_hours }}</td>

                    <td>
                        <a href="/attendance/detail/{{ $attendance->id }}" class="btn-info">詳細</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection