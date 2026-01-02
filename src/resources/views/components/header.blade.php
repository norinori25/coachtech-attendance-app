<header class="header">
    <div class="header__logo">
        <img src="{{ asset('img/logo.svg') }}" alt="ロゴ">
    </div>
    @if( !in_array(Route::currentRouteName(), ['register', 'login', 'verification.notice']) )
    
    <nav class="header__nav">
        <ul>
            @if(Auth::check())
                <li><a href="/admin/attendance/list">勤怠一覧</a></li>
                <li><a href="/admin/staff/list">スタッフ一覧</a></li>
                <li><a href="/stamp_correction_request/list">申請一覧</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="header__logout">ログアウト</button>
                    </form>
                </li>
            @endif
        </ul>
    </nav>
    @endif
</header>