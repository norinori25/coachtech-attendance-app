@extends('layouts.default')

@section('title', '申請一覧画面（一般ユーザー）')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/request.css') }}">
@endsection

@section('content')

@include('components.header')
<div class="container">
    <h1 class="request-title">
        <span class="request-title__line"></span>申請一覧
    </h1>

    <div class="border">
        <ul class="border__list">
            <li>
                <a href="{{ url('/stamp_correction_request/list?status=pending') }}"
                   class="btn-outline-primary {{ request('status') === 'pending' ? 'active' : '' }}">
                   承認待ち
                </a>
            </li>
            @if(!auth()->guest())
            <li>
                <a href="{{ url('/stamp_correction_request/list?status=approved') }}"
                    class="btn-outline-success {{ request('status') === 'approved' ? 'active' : '' }}">
                    承認済み
                </a>
            </li>
            @endif
        </ul>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
                <tr>
                    <td>{{ $request->status }}</td>
                    <td>{{ $request->user->name }}</td>
                    <td>{{ $request->attendance_date ? $request->attendance_date->format('Y/m/d') : '' }}</td>
                    <td>{{ $request->reason }}</td>
                    <td>{{ $request->created_at->format('Y/m/d') }}</td>
                    <td>
                        <a href="{{ route('attendance_request.show', $request->id) }}" class="btn-info">詳細</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection