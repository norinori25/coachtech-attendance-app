@extends('layouts.default')

@section('title','勤怠登録画面')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance.css')  }}">
@endsection

@section('content')

<div class="attendance-wrapper">
    <div class="attendance-card">

        <p class="status">{{ $status }}</p>

        @php
            $weekMap = ['日','月','火','水','木','金','土'];
            $weekday = $weekMap[now()->dayOfWeek];
        @endphp

        <p class="date">{{ now()->format('Y年m月d日') }} ({{ $weekday }})</p>

        <p class="time">{{ now()->format('H:i') }}</p>

        @if($status === '勤務外')
            <form method="POST" action="/attendance">
                @csrf
                <button class="btn" type="submit" name="action" value="start">出勤</button>
            </form>
        @endif

        @if($status === '出勤中')
            <form method="POST" action="/attendance">
                @csrf
                <div class="btn-row">
                    <button class="btn" type="submit" name="action" value="end">退勤</button>
                    <button class="btn break-btn" type="submit" name="action" value="break_in">休憩入</button>
                </div>
            </form>
        @endif

        @if($status === '休憩中')
            <form method="POST" action="/attendance">
                @csrf
                <button class="btn break-btn" type="submit" name="action" value="break_out">休憩戻</button>
            </form>
        @endif

        @if($status === '退勤済')
            <p class="finish">お疲れ様でした。</p>
        @endif

    </div>
</div>
@endsection
