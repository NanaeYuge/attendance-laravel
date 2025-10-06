<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','勤怠管理（管理者）')</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/admin/layout.css') }}?v={{ now()->format('YmdHi') }}">

    @if (request()->routeIs('admin.login.form') || request()->is('admin/login'))
        <link rel="stylesheet" href="{{ asset('css/admin/auth.css') }}?v={{ now()->format('YmdHi') }}">
    @endif

    @yield('head')
</head>
<body class="role-admin">

<header class="admin-header">
    <div class="admin-header__inner">
        <a href="{{ url('/admin/attendance') }}" class="admin-header__brand" aria-label="COACHTECH Admin Home">
            {{-- storage:link 済みで storage/app/public/CoachTech_White 1-2.png を参照 --}}
            <img
                src="{{ asset('storage/CoachTech_White%201-2.png') }}"
                alt="COACHTECH"
                class="admin-header__logo"
                width="200" height="36">
        </a>

        @auth('admin')
            <nav class="admin-nav" aria-label="管理者メニュー">
                <a class="admin-nav__link" href="{{ route('admin.attendance.daily') }}">勤怠一覧</a>
                <a class="admin-nav__link" href="{{ route('admin.staff.index') }}">スタッフ一覧</a>
                <a class="admin-nav__link" href="{{ route('admin.requests.index') }}">申請一覧</a>
                <form method="POST" action="{{ route('admin.logout') }}" class="admin-nav__logout">
                    @csrf
                    <button type="submit" class="admin-nav__logoutBtn">ログアウト</button>
                </form>
            </nav>
        @endauth
    </div>
</header>

<main class="admin-main">
    @yield('content')
</main>

@yield('scripts')
</body>
</html>
