@extends('admin.layouts.app')
@section('title','管理者ログイン')

@section('content')
<section class="auth-section">
    <h1 class="auth-title">管理者ログイン</h1>

    <form method="POST" action="{{ route('admin.login') }}" class="auth-form" novalidate>
    @csrf

    <div class="form-group">
        <label for="email" class="form-label">メールアドレス</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-input" autocomplete="username" autofocus>
        @error('email')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div class="form-group">
        <label for="password" class="form-label">パスワード</label>
        <input id="password" type="password" name="password" class="form-input" autocomplete="current-password">
        @error('password')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <button type="submit" class="btn-primary">管理者ログインする</button>
    </form>
</section>
@endsection
