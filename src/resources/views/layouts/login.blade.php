<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CT COACHTECH</title>
    <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css" />
    <link rel="stylesheet" href="{{ asset('css/login.css')}}?v={{ time() }}">
    @yield('css')
</head>

<body>
    <div class="auth">
        <header class="header">
            <div class="header_img">
                <img src="{{ asset('storage/images/logo.svg') }}" alt="ロゴ画像">
            </div>
            @yield('link')
        </header>
        <div class="content">
            @yield('content')
        </div>
    </div>
</body>

</html>