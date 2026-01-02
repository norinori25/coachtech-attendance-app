@extends('layouts.default')

@section('title', '勤怠一覧（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance.css')  }}">
@endsection

@section('content')

@include('components.admin_header')
<div class="container">
    {{-- タイトル --}}
    <h1 class="attendance-title">
        <span class="attendance-title__line"></span>{{ now()->format('Y年m月d日') }} の勤怠
    </h1>

    {{-- カレンダーバー --}}
    <div class="calendar-bar">
        <div class="calendar-bar__prev">
            <a href="{{ route('admin.attendance.index', ['date' => $currentDate->copy()->subDay()->toDateString()]) }}">← 前日
            </a>
        </div>

        <div class="calendar-bar__current">
            <span class="calendar-icon">📅</span>
            {{ $currentDate->format('Y/m/d') }}
        </div>

        <div class="calendar-bar__next">
            <a href="{{ route('admin.attendance.index', ['date' => $currentDate->copy()->addDay()->toDateString()]) }}">翌日 →
            </a>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>名前</th>
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
                    <td>{{ $attendance->user->name }}</td>
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
                        <a href="{{ url('/admin/attendance/' . $attendance->id) }}" class=" btn-info">詳細</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection