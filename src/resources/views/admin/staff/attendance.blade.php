@extends('layouts.default')

@section('title', 'スタッフ別勤怠一覧（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/staff_attendance.css')  }}">
@endsection

@section('content')

<div class="container">
    {{-- タイトル --}}
    <h1 class="attendance-title">
        <span class="attendance-title__line"></span>
        {{ $user->name }} さんの勤怠一覧
    </h1>

    {{-- カレンダーバー --}}
    <div class="calendar-bar">
        <div class="calendar-bar__prev">
            <a href="{{ route('admin.attendance.staffList', ['id' => $user->id, 'month' => $currentMonth->copy()->subMonth()->format('Y-m')]) }}">← 前月</a>
        </div>
        <div class="calendar-bar__current">
            <span class="calendar-icon">📅</span>
            {{ $currentMonth->format('Y/m') }}
        </div>
        <div class="calendar-bar__next">
            <a href="{{ route('admin.attendance.staffList', ['id' => $user->id, 'month' => $currentMonth->copy()->addMonth()->format('Y-m')]) }}">翌月 →</a>
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
                    <td>{{ \Carbon\Carbon::parse($attendance->date)->locale('ja')->isoFormat('MM/DD(dd)') }}</td>
                    <td>{{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '' }}</td>
                    <td>{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}</td>
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
                    <td>{{ $attendance->total_hours }}</td>
                    <td>
                        <a href="{{ url('/admin/attendance/' . $attendance->id) }}" class="btn-info">詳細</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="text-end mt-2">
        <form action="{{ route('admin.attendance.exportCsv', $user->id) }}" method="GET">
            <button type="submit" class="btn btn-dark">CSV出力</button>
        </form>
    </div>
</div>
@endsection