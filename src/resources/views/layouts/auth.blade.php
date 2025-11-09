<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CT COACHTECH</title>
    <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css" />
    <link rel="stylesheet" href="{{ asset('css/user.css')}}?v={{ time() }}">
    @yield('css')
</head>

<body>
    <div class="auth">
        <header class="header">
            <div class="header_img">
                <img src="{{ asset('storage/images/logo.svg') }}" alt="ロゴ画像">
            </div>

            <div class="header_menu">
                @if(Auth::guard('admin')->check())
                <a href="{{ route('admin.attendance.index') }}">勤怠一覧</a>
                <a href="{{ route('admin.staff.index') }}">スタッフ一覧</a>
                <a href="{{ route('admin.request.index') }}">申請一覧</a>
                @endif
                <form id="logout-form" method="post" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">ログアウト</button>
                </form>
            </div>
            @yield('link')
        </header>

        <div class="content">
            @yield('content')
        </div>
    </div>
</body>

</html>