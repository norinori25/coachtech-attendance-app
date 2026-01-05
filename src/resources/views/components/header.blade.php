<header class="header">
    <div class="header__logo">
        @auth
            @can('admin')
                <a href="{{ route('admin.attendance.index') }}">
                    <img src="{{ asset('img/logo.svg') }}" alt="管理者ロゴ">
                </a>
            @else
                <a href="{{ url('/attendance') }}">
                    <img src="{{ asset('img/logo.svg') }}" alt="ロゴ">
                </a>
            @endcan
        @else
            <img src="{{ asset('img/logo.svg') }}" alt="ロゴ">
        @endauth
    </div>

    {{-- ログイン画面・登録画面ではヘッダー非表示 --}}
    @if(!in_array(Route::currentRouteName(), ['register', 'login', 'admin.login', 'verification.notice']))
    <nav class="header__nav">
        <ul>
            @auth
                @can('admin')
                    {{-- 管理者メニュー --}}
                    <li><a href="{{ route('admin.attendance.index') }}">勤怠一覧</a></li>
                    <li><a href="{{ route('admin.staff.index') }}">スタッフ一覧</a></li>
                    <li><a href="{{ route('admin.attendance_request.index') }}">申請一覧</a></li>
                @else
                    {{-- 一般ユーザーメニュー --}}
                    @php
                    // コントローラで渡している $status（勤務外 / 出勤中 / 休憩中 / 退勤済）
                    $status = $status ?? null;
                    @endphp

                    {{-- 退勤前だけ表示 --}}
                    @if($status !== '退勤済')
                        <li><a href="{{ url('/attendance') }}">勤怠</a></li>
                    @endif

                    {{-- 勤怠一覧（文言切り替え） --}}
                    @if($status !== '退勤済')
                        <li><a href="{{ route('attendance.index') }}">勤怠一覧</a></li>
                    @else
                        <li><a href="{{ route('attendance.index') }}">今月の出勤一覧</a></li>
                    @endif

                    {{-- 申請（文言切り替え） --}}
                    @if($status !== '退勤済')
                        <li><a href="{{ route('attendance_request.index') }}">申請</a></li>
                    @else
                        <li><a href="{{ route('attendance_request.index') }}">申請一覧</a></li>
                    @endif

                @endcan

                {{-- ログアウト --}}
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="header__logout">ログアウト</button>
                    </form>
                </li>
            @endauth
        </ul>
    </nav>
    @endif
</header>