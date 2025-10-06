@extends('staff.layouts.app')
@section('title','会員登録')

@section('content')
<section class="auth-viewport">
    <div class="auth-section register-card">
    <h1 class="auth-title">会員登録</h1>

    <form method="POST" action="{{ route('register') }}" class="auth-form" novalidate>
        @csrf

        <div class="form-group">
        <label for="name" class="form-label-lg">名前</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" class="input-md">
        @error('name')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label-lg">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="input-md">
            @error('email')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label-lg">パスワード</label>
            <input id="password" type="password" name="password" class="input-md">
            @error('password')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label-lg">パスワード確認</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="input-md">
            @error('password_confirmation')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="btn-primary btn-lg w-100">登録する</button>
    </form>

        <p class="auth-link link-login"><a href="{{ route('login') }}">ログインはこちら</a></p>
    </div>
</section>
@endsection
