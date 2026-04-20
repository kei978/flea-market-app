@extends('layouts.app', [
    'showSearch' => false,
    'authButtons' => 'none',
])

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
@endsection

@section('content')
    <div class="card">
        <div class="card__title">
            <h1>会員登録</h1>
        </div>
        <form action="/register" class="form" method="POST">
            @csrf
            {{-- ユーザー名 --}}
            <div class="form__group">
                <div class="form__group-title">
                    <h2 class="form__label">ユーザー名</h2>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="name" value="{{ old('name') }}" class="form__input--text-input">
                    </div>
                    <div class="form__error">
                        @foreach ($errors->get('name') as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                </div>
            </div>
            {{-- メールアドレス --}}
            <div class="form__group">
                <div class="form__group-title">
                    <h2 class="form__label">メールアドレス</h2>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="email" value="{{ old('email') }}" class="form__input--text-input">
                    </div>
                    <div class="form__error">
                        @foreach ($errors->get('email') as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                </div>
            </div>
            {{-- パスワード --}}
            <div class="form__group">
                <div class="form__group-title">
                    <h2 class="form__label">パスワード</h2>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="password" name="password" class="form__input--text-input">
                    </div>
                    <div class="form__error">
                        @foreach ($errors->get('password') as $error)
                            @if ($error !== 'パスワードと一致しません')
                                {{ $error }}<br>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            {{-- 確認用パスワード --}}
            <div class="form__group">
                <div class="form__group-title">
                    <h2 class="form__label">確認用パスワード</h2>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="password" name="password_confirmation" class="form__input--text-input">
                    </div>
                    <div class="form__error">
                        @foreach ($errors->get('password_confirmation') as $error)
                            {{ $error }}<br>
                        @endforeach
                        @if ($errors->first('password') === 'パスワードと一致しません')
                            パスワードと一致しません<br>
                        @endif
                    </div>
                </div>
            </div>
            {{-- 登録ボタン --}}
            <div class="form__button">
                <button class="form__button-submit" type="submit">登録する</button>
            </div>
            {{-- ログイン画面遷移ボタン --}}
            <div class="form__link">
                <a href="/login" class="form__link-item">ログインはこちら</a>
            </div>
        </form>
    </div>
@endsection
