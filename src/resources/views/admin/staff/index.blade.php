@extends('layouts.default')

@section('title', 'スタッフ一覧（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/staff.css') }}">
@endsection

@section('content')

<div class="container">
    <h1 class="staff-title">
        <span class="staff-title__line"></span>スタッフ一覧
    </h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>月次勤怠</th>
            </tr>
        </thead>

        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <a href="{{ route('admin.attendance.staffList', ['id' => $user->id]) }}" class="btn-info">詳細</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
</div>
@endsection