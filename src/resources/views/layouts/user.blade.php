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
                <a href="{{ route('attendance.index') }}">勤怠</a>
                @if(auth()->user()->role === 'user')
                <a href="{{ route('attendance.list') }}">勤怠一覧</a>
                <a href="{{ route('applications.index') }}">申請</a>
                @endif
                <form id="logout-form" method="POST" action="{{ route('logout') }}">
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