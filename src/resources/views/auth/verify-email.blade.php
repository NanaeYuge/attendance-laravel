@extends('staff.layouts.app')
@section('title','メール認証')

@section('content')
<div class="verify-container">
    <p class="verify-lead">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>


    <a class="verify-cta" href="{{ env('MAIL_CLIENT_URL', 'http://localhost:8025') }}" target="_blank" rel="noopener">
    認証はこちらから
    </a>

    @if (session('status') === 'verification-link-sent')
    <div class="alert success">
        新しい認証リンクを送信しました。受信トレイをご確認ください。
    </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="resend-form">
    @csrf
    <button type="submit" class="resend-link">認証メールを再送する</button>
    </form>
</div>

<style>
    :root{
    --blue:#0073CC;
    --black:#000;
    }
    .verify-container{
    max-width: 720px;
    margin: 0 auto;
    text-align: center;
    padding-top: 220px;
    padding-bottom: 48px;
    font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans JP", "Hiragino Kaku Gothic ProN", Meiryo, sans-serif;
    }


    .verify-lead{
    width: 720px;
    margin: 0 auto 64px;
    font-weight: 700;
    font-size: 22px;
    line-height: 1.3;
    color: var(--black);
    }

    .verify-cta{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 257px;
    height: 69px;
    border-radius: 10px;
    background: #D9D9D9;
    border: 1px solid var(--black);
    color: var(--black);
    font-weight: 700;
    font-size: 20px;
    text-decoration: none;
    letter-spacing: 0;
    margin: 0 auto 28px;
    transition: transform .02s ease, filter .2s ease;
    }
    .verify-cta:hover{ filter: brightness(.98); }
    .verify-cta:active{ transform: translateY(1px); }

    .alert.success{
    max-width: 720px;
    margin: 0 auto 8px;
    padding: 12px 16px;
    background: #E6FFED;
    color: #05630E;
    border-radius: 8px;
    font-weight: 700;
    }

    .resend-form{ margin-top: 12px; }
    .resend-link{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 257px;
    height: 54px;
    background: transparent;
    border: none;
    color: var(--blue);
    font-size: 20px;
    font-weight: 400;
    letter-spacing: 0;
    line-height: 1;
    text-decoration: underline;
    cursor: pointer;
    padding: 0;
    }

    @media (max-width: 760px){
    .verify-container{ padding: 120px 16px 40px; }
    .verify-lead{ width: auto; font-size: 18px; }
    }
</style>

@endsection