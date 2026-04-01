@extends('layouts.app', [
    'showSearch' => false,
    'authButtons' => 'none',
])

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('content')
    <div class="card">
        <div class="card__title">
            <h2>ログイン</h2>
        </div>
        <form action="{{ route('login') }}" class="form" method="POST">
            @csrf
            {{-- メールアドレス --}}
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label">メールアドレス</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="email" value="{{ old('email') }}" class="form__input--text-input">
                    </div>
                    <div class="form__error">
                        @error('email')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            {{-- パスワード --}}
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label">パスワード</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="password" name="password" class="form__input--text-input">
                    </div>
                    <div class="form__error">
                        @error('password')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            {{-- ログインボタン --}}
            <div class="form__button">
                <button class="form__button-submit" type="submit">ログインする</button>
            </div>
            {{-- 会員登録画面遷移ボタン --}}
            <div class="form__link">
                <a href="/register" class="form__link-item">会員登録はこちら</a>
            </div>
        </form>
    </div>
@endsection
