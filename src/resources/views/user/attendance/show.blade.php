@extends('layouts.default')

@section('title', '勤怠詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance_detail.css') }}">
@endsection

@section('content')

<div class="container">

    <h1 class="attendance-title">
        <span class="attendance-title__line"></span>
        勤怠詳細
    </h1>

    {{-- 申請が存在する場合 --}}
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
                    <th>休憩{{ $index+1 }}</th>
                    <td>{{ optional($break->break_start)->format('H:i') }}</td>
                    <td class="time-separator">～</td>
                    <td>{{ optional($break->break_end)->format('H:i') ?? '' }}</td>
                    <td></td>
                </tr>
            @endforeach
            <tr>
                <th>備考</th>
                <td colspan="3">{{ $attendanceRequest->attendance->note ?? '' }}</td>
                <td></td>
            </tr>
        </table>

        {{-- ステータス表示（一般ユーザーのみ） --}}
        @if(!auth()->user()->is_admin)
            @if($attendanceRequest->status === '承認待ち')
                <div class="text-warning">＊承認待ちのため修正はできません。</div>
            @elseif($attendanceRequest->status === '承認済み')
                <div class="text-success">＊この勤怠修正は承認済みです。</div>
            @endif
        @endif

        {{-- 管理者用承認ボタン --}}
        @if(auth()->user()->is_admin)
            @if($attendanceRequest->status === '承認待ち')
                <form action="{{ route('admin.attendance_request.approve', $attendanceRequest->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-dark">承認</button>
                </form>
            @elseif($attendanceRequest->status === '承認済み')
                <button type="button" class="btn btn-secondary" disabled>承認済み</button>
            @endif
        @endif

    {{-- 申請が存在しない場合 --}}
    @else
        <form action="{{ route('attendance_request.store') }}" method="POST" novalidate>
            @csrf
            <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">

            <table class="table attendance-detail-table">
                <tr>
                    <th>名前</th>
                    <td>{{ $attendance->user->name }}</td>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <th>日付</th>
                    <td class="year-cell">{{ optional($attendance->date)->format('Y年') }}</td>
                    <td></td>
                    <td class="monthday-cell">{{ optional($attendance->date)->format('m月d日') }}</td>
                    <td></td>
                </tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        <input type="time" id="start_time_new" name="start_time_new" value="{{ old('start_time_new', optional($attendance->start_time)->format('H:i')) }}">
                        @error('start_time_new')
                            <div class="form__error">{{ $message }}</div>
                        @enderror
                    </td>
                    <td class="time-separator">～</td>
                    <td>
                        <input type="time" id="end_time_new" name="end_time_new" value="{{ old('end_time_new', optional($attendance->end_time)->format('H:i')) }}">
                        @error('end_time_new')
                            <div class="form__error">{{ $message }}</div>
                        @enderror
                    </td>
                    <td></td>
                </tr>
                @php
                    $firstBreak = $attendance->breakRecords->first();
                @endphp
                <tr>
                    <th>休憩</th>
                    <td>
                        <input type="time" id="break_start_new" name="break_start_new" value="{{ old('break_start_new', optional($firstBreak?->break_start)->format('H:i')) }}">
                    </td>
                    <td class="time-separator">～</td>
                    <td>
                        <input type="time" id="break_end_new" name="break_end_new" value="{{ old('break_end_new', optional($firstBreak?->break_end)->format('H:i')) }}">
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th>休憩{{ $attendance->breakRecords->count() ? $attendance->breakRecords->count() : 1 }}</th>
                    <td><input type="time" id="break_start_new2" name="break_start_new" value="{{ old('break_start_new') }}"></td>
                    <td class="time-separator">～</td>
                    <td><input type="time" id="break_end_new2" name="break_end_new" value="{{ old('break_end_new') }}"></td>
                    <td></td>
                </tr>
                <tr>
                    <th>備考</th>
                    <td colspan="3">
                        <textarea name="note" class="note-field" required>{{ old('note', $attendance->note ?? '') }}</textarea>
                        @error('note')
                            <div class="form__error">{{ $message }}</div>
                        @enderror
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

{{-- JSで未入力時に--:--を消す --}}
@section('scripts')
<script>
  document.querySelectorAll('input[type="time"]').forEach(el => {
    el.addEventListener('input', () => {
      if (!el.value) {
        el.placeholder = '';
      }
    });
  });
</script>
@endsection

@endsection