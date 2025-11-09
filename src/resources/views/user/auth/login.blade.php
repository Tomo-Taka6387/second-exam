@extends('layouts.login')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/auth/login.css')}}?v={{ time() }}">
@endsection

@section('content')
<div class="login-form">
    <h1 class="login-form_title">ログイン</h1>
    <div class="login-form__inner">
        <form class="login-form__form" action="{{ route('user.submit') }}" method="post">
            @csrf
            <div class="login-form__group">
                <label class="login-form__label" for="email">メールアドレス</label>
                <input class="login-form__input" type="email" name="email" id="email">
                <p class="login-form__error-message">
                    @error('email')
                    {{ $message }}
                    @enderror
                </p>
            </div>
            <div class="login-form__group">
                <label class="login-form__label" for="password">パスワード</label>
                <input class="login-form__input" type="password" name="password" id="password">
                <p class="login-form__error-message">
                    @error('password')
                    {{ $message }}
                    @enderror
                </p>
            </div>
            <div class=" form_button">
                <button class="login-form__btn" type="submit">ログインする</button>
                <a href="/register" class="register_btn">会員登録はこちら</a>
            </div>
        </form>
    </div>
</div>
@endsection