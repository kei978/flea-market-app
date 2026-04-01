<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Flea Market App</title>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            {{-- 左：ロゴ --}}
            <div class="header__left">
                <a href="/" class="header__logo">
                    <img src="{{ asset('images/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH">
                </a>
            </div>

            {{-- 中央：検索欄（必要な画面のみ） --}}
            <div class="header__center">
                @if (($showSearch ?? false) === true)
                    <form action="/" method="GET" class="header__search-form">
                        <input type="text" name="keyword" value="{{ request('keyword') }}" class="header__search-input" placeholder="なにをお探しですか？">
                    </form>
                @endif
            </div>

            {{-- 右：ボタン類 --}}
            <div class="header__right">
                {{-- ログイン前（商品一覧・商品詳細） --}}
                @if (($authButtons ?? 'none') === 'guest')
                    <a href="{{ route('login') }}" class="header__btn">ログイン</a>
                    <a href="{{ route('login') }}" class="header__btn">マイページ</a>
                    <a href="{{ route('login') }}" class="header__btn header__btn--primary">出品</a>
                    {{-- ログイン後 --}}
                @elseif(($authButtons ?? 'none') === 'auth')
                    <form action="/logout" method="POST">
                        @csrf
                        <button class="header__btn">ログアウト</button>
                    </form>
                    <a href="{{ route('mypage.index') }}" class="header__btn">マイページ</a>
                    <a href="{{ route('sell.index') }}" class="header__btn header__btn--primary">出品</a>
                    {{-- ログイン画面・会員登録画面（ロゴのみ） --}}
                @endif
            </div>
        </div>
    </header>

    <main class="main">
        @yield('content')
    </main>
</body>

</html>
