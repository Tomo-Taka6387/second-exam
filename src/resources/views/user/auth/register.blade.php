@extends('layouts.login')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/auth/register.css')}}?v={{ time() }}">
@endsection

@section('content')
<div class="register-form">
    <h1 class="register-form_title">会員登録</h1>
    <div class="register-form__inner">
        <form class="register-form__form" action="{{ route('register.submit') }}" method="post">
            @csrf
            <div class="register-form__group">
                <label class="register-form__label" for="name">名前</label>
                <input class="register-form__input" type="text" name="name" id="name">
                <p class="register-form__error-message">
                    @error('name')
                    {{ $message }}
                    @enderror
                </p>
            </div>
            <div class="register-form__group">
                <label class="register-form__label" for="email">メールアドレス</label>
                <input class="register-form__input" type="email" name="email" id="email">
                <p class="register-form__error-message">
                    @error('email')
                    {{ $message }}
                    @enderror
                </p>
            </div>
            <div class="register-form__group">
                <label class="register-form__label" for="password">パスワード</label>
                <input class="register-form__input" type="password" name="password" id="password">
                @error('password')
                @if($message !== 'パスワードと一致しません')
                <p class="register-form__error-message">{{ $message }}</p>
                @endif
                @enderror
            </div>
            <div class="register-form__group">
                <label class="register-form__label" for="password_confirmation">パスワード確認</label>
                <input class="register-form__input" type="password" name="password_confirmation" id="password_confirmation">
                @if($errors->has('password') && $errors->first('password') === 'パスワードと一致しません')
                <p class="register-form__error-message">
                    {{ $errors->first('password') }}
                </p>
                @endif
            </div>
            <div class="form_button">
                <button class="register-form__btn btn" type="submit">登録する</button>
                <a href="/login" class="login_btn">ログインはこちら</a>
            </div>
        </form>
    </div>
</div>
@endsection