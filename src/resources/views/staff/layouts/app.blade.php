<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title','勤怠管理')</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@700&display=swap" rel="stylesheet">

    @yield('head')

    <style>
        .site-header {
            height: 80px;
            background: #000;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
        }
        .site-header .logo {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .site-header .logo-img {
            height: 32px;
            display: block;
        }
        .site-header nav a,
        .site-header nav button.link-like {
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            margin-left: 24px;
        }
        .site-header nav button.link-like {
            background: none;
            border: 0;
            padding: 0;
            cursor: pointer;
            font: inherit;
        }

        body.role-staff {}
        .page-main.container { max-width: 1200px; margin: 0 auto; padding: 24px; }
    </style>
</head>
<body class="role-staff">
<header class="site-header">
    @php
        $logoPath = 'CoachTech_White 1-2.png';
        $logoUrl  = asset('storage/' . rawurlencode($logoPath));
    @endphp
    <a href="{{ url('/attendance') }}" class="logo" aria-label="COACHTECH">
        <img class="logo-img" src="{{ $logoUrl }}" alt="COACHTECH">
    </a>

    <nav>
        @auth
            <a href="{{ route('staff.attendance.create') }}">勤怠</a>
            <a href="{{ route('staff.attendance.index') }}">勤怠一覧</a>
            <a href="{{ route('staff.requests.index') }}">申請</a>
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button class="link-like">ログアウト</button>
            </form>
        @endauth
    </nav>
</header>

<main class="container page-main">
    @yield('content')
</main>
</body>
</html>
