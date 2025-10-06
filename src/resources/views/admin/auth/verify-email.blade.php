@extends('staff.layouts.app')
@section('title','メール認証')
@section('content')
<h1>メール認証</h1>
<p>会員登録ありがとうございます。続行するには、メールに送信されたリンクでメールアドレスを認証してください。</p>
@if(session('status')=='verification-link-sent')
  <div class="alert success">認証リンクを再送しました。メールをご確認ください。</div>
@endif
<form method="POST" action="{{ route('verification.send') }}">@csrf<button class="btn">認証メール再送</button></form>
<form method="POST" action="{{ route('logout') }}" style="margin-top:16px">@csrf<button class="btn-secondary">ログアウト</button></form>
@endsection
