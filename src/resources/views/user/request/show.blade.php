@extends('layouts.default')

@section('title', '申請詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance_detail.css') }}">
@endsection

@section('content')

@include('components.header')

<div class="container">

    <h1 class="attendance-title">
        <span class="attendance-title__line"></span>
        申請詳細
    </h1>

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
                <th>休憩{{ $index+1 }}</th>
                <td>{{ optional($break->break_start)->format('H:i') }}</td>
                <td class="time-separator">～</td>
                <td>{{ optional($break->break_end)->format('H:i') }}</td>
                <td></td>
            </tr>
        @endforeach

        <tr>
            <th>申請理由</th>
            <td colspan="3">{{ $attendanceRequest->reason }}</td>
            <td></td>
        </tr>

        <tr>
            <th>ステータス</th>
            <td colspan="3">{{ $attendanceRequest->status }}</td>
            <td></td>
        </tr>
    </table>

    {{-- ステータス表示（一般ユーザーのみ） --}}
    @if($attendanceRequest->status === '承認待ち')
        <div class="text-warning">＊承認待ちのため修正はできません。</div>
    @elseif($attendanceRequest->status === '承認済み')
        <div class="text-success">＊この勤怠修正は承認済みです。</div>
    @endif

</div>

@endsection