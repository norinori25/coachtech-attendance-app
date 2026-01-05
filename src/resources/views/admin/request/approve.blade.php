@extends('layouts.default')

@section('title', '修正申請（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance_detail.css') }}">
@endsection

@section('content')

<div class="container">

    <h1 class="attendance-title">
        <span class="attendance-title__line"></span>
        勤怠詳細
    </h1>

    <table class="table attendance-detail-table">
        <tr>
            <th>名前</th>
            <td>{{ $request->user->name }}</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <th>日付</th>
            <td class="year-cell">{{ \Carbon\Carbon::parse($request->attendance->date)->format('Y年') }}</td>
            <td></td>
            <td class="monthday-cell">{{ \Carbon\Carbon::parse($request->attendance->date)->format('m月d日') }}</td>
            <td></td>
        </tr>
        <tr>
            <th>出勤・退勤</th>
            <td>{{ optional($request->attendance->start_time)->format('H:i') }}</td>
            <td class="time-separator">～</td>
            <td>{{ optional($request->attendance->end_time)->format('H:i') }}</td>
            <td></td>
        </tr>
        @foreach($request->attendance->breakRecords as $index => $break)
            <tr>
                <th>休憩{{ $index+1 }}</th>
                <td>{{ optional($break->break_start)->format('H:i') }}</td>
                <td class="time-separator">～</td>
                <td>{{ optional($break->break_end)->format('H:i') }}</td>
                <td></td>
            </tr>
        @endforeach
        <tr>
            <th>備考</th>
            <td colspan="3">{{ $request->reason }}</td>
            <td></td>
        </tr>
    </table>

    {{-- 管理者用承認ボタン --}}
    @if($request->status === '承認待ち')
        <form action="{{ route('admin.attendance_request.approve', $request->id) }}" method="POST" class="mt-3">
            @csrf
            <button type="submit" class="btn btn-dark">承認</button>
        </form>
    @else
        <div class="mt-3">
            <a href="{{ route('admin.attendance_request.index') }}" class="btn btn-secondary mt-2">承認済</a>
        </div>
    @endif

</div>

@endsection