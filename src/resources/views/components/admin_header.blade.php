<header class="header">
    <div class="header__logo">
        <a href="{{ route('admin.staff.index') }}">
            <img src="{{ asset('img/logo.svg') }}" alt="管理者ロゴ">
        </a>
    </div>

    <nav class="header__nav">
        <ul>
            @auth
                @can('admin')
                    <li><a href="{{ route('admin.attendance.index') }}">勤怠一覧</a></li>
                    <li><a href="{{ route('admin.staff.index') }}">スタッフ一覧</a></li>
                    <li>
                        <a href="{{ url('/admin/stamp_correction_request/list?status=pending') }}">申請一覧</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="header__logout">ログアウト</button>
                        </form>
                    </li>
                @endcan
            @endauth
        </ul>
    </nav>
</header>
