@extends('layouts.default')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/auth.css')  }}">
@endsection

@section('content')

<div class="verify-container">
    <p class="verify-message">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>

    {{-- 認証ページへ（GETリンク） --}}
    <div class="verify-form">
        <a href="{{ route('verification.notice') }}" class="verify-button">
            認証はこちらから
        </a>
    </div>

    {{-- 再送リンク（POSTフォーム） --}}
    <form method="POST" action="{{ route('verification.send') }}" class="resend-form">
        @csrf
        <button type="submit" class="resend-button">
            認証メールを再送する
        </button>
    </form>
</div>
@endsection