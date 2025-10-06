@extends('staff.layouts.app')
@section('title','ログイン')

@section('content')
<section class="auth-viewport">
    <div class="auth-section login-card">
    <h1 class="auth-title">ログイン</h1>

    <form method="POST" action="{{ route('login') }}" class="auth-form" novalidate>
        @csrf

        <div class="form-group">
        <label for="email" class="form-label-lg">メールアドレス</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" autofocus class="input-lg">
        @error('email')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
        <label for="password" class="form-label-lg">パスワード</label>
        <input id="password" type="password" name="password" class="input-lg">
        @error('password')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="btn-primary btn-lg w-100">ログインする</button>
    </form>

    <p class="auth-link"><a href="{{ route('register') }}">会員登録はこちら</a></p>
    </div>
</section>
@endsection
