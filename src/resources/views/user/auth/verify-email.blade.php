@extends('layouts.login')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/auth/verify.css')}}?v={{ time() }}">
@endsection

@section('content')
<div class="mail_notice--div">
    <div class="mail_notice--content">
        @if (session('resent'))
        <p class="notice_resend--alert">
            認証メールを送信しました！
        </p>
        @endif

        <p class="alert_message">
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            メール認証を完了してください。
        </p>

        <form class="mail_resend--form" method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="mail_resend--button">認証はこちらから</button>

            <a href="#"
                class="mail_resend--again"
                onclick="event.preventDefault(); this.closest('form').submit();">
                認証メールを再送する
            </a>
        </form>
    </div>
</div>
@endsection