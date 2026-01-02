@extends('layouts.default')

@section('title', '修正申請承認（管理者）')

@section('content')
@include('components.admin_header')

<div class="container">
    <h1>修正申請承認（管理者）</h1>

    <table class="table table-bordered">
        <tr>
            <th>申請ID</th>
            <td>{{ $request->id }}</td>
        </tr>
        <tr>
            <th>ユーザー名</th>
            <td>{{ $request->user->name }}</td>
        </tr>
        <tr>
            <th>対象日</th>
            <td>{{ $request->attendance->date ?? '' }}</td>
        </tr>
        <tr>
            <th>申請内容</th>
            <td>{{ $request->reason }}</td>
        </tr>
        <tr>
            <th>ステータス</th>
            <td>{{ $request->status }}</td>
        </tr>
    </table>

    {{-- 承認フォーム --}}
    @if($request->status === '承認待ち')
    <form action="{{ route('admin.attendance_request.approve', $request->id) }}" method="post" class="mt-3">
        @csrf
        <button type="submit" class="btn btn-success">承認する</button>
        <a href="{{ route('admin.attendance_request.index') }}" class="btn btn-secondary">一覧に戻る</a>
    </form>
@else
    <div class="mt-3">
        <span class="text-muted">この申請は既に {{ $request->status }} です。</span><br>
        <a href="{{ route('admin.attendance_request.index') }}" class="btn btn-secondary mt-2">一覧に戻る</a>
    </div>
@endif
</div>
@endsection