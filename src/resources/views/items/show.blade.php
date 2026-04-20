@extends('layouts.app', [
    'showSearch' => true,
    'authButtons' => Auth::check() ? 'auth' : 'guest',
])

@section('css')
    <link rel="stylesheet" href="{{ asset('css/items/show.css') }}">
@endsection

@section('content')
    <div class="item-show">
        {{-- 商品画像 --}}
        <div class="item-show__left">
            <img src="{{ $item->image_url }}" alt="商品画像" class="item-show__image">
        </div>
        {{-- 商品情報 --}}
        <div class="item-show__right">
            {{-- 商品名・ブランド名 --}}
            <h1 class="item-show__title">{{ $item->title }}</h1>
            <p class="item-show__brand">{{ $item->brand }}</p>
            {{-- 価格 --}}
            <p class="item-show__price">¥{{ number_format($item->price) }}（税込）</p>
            {{-- いいね・コメント数 --}}
            <div class="item-show__icons">
                {{-- いいね --}}
                @auth
                    <form action="{{ route('items.like', $item->id) }}" method="POST" class="item-show__like-form">
                        @csrf
                        <button type="submit" class="item-show__like-button">
                            <div class="icon-wrapper">
                                @if ($item->isLikedBy(Auth::user()))
                                    <img src="{{ asset('images/ハートロゴ_ピンク.png') }}" class="icon-heart">
                                @else
                                    <img src="{{ asset('images/ハートロゴ_デフォルト.png') }}" class="icon-heart">
                                @endif
                                <span class="icon-number">{{ $item->likedUsers->count() }}</span>
                            </div>
                        </button>
                    </form>
                @else
                    <div class="item-show__like-disabled">
                        <div class="icon-wrapper">
                            <img src="{{ asset('images/ハートロゴ_デフォルト.png') }}" class="icon-heart">
                            <span class="icon-number">{{ $item->likedUsers->count() }}</span>
                        </div>
                    </div>
                @endauth
                {{-- コメント数 --}}
                <div class="item-show__comment-count">
                    <div class="icon-wrapper">
                        <img src="{{ asset('images/ふきだしロゴ.png') }}" class="icon-comment">
                        <span class="icon-number">{{ $item->comments->count() }}</span>
                    </div>
                </div>
            </div>
            {{-- 購入ボタン --}}
            <div class="item-show__purchase">
                @if ($item->status == 1)
                    <a href="{{ route('purchase.index', $item->id) }}" class="item-show__purchase-button">
                        購入手続きへ
                    </a>
                @else
                    <span class="item-show__purchase-button item-show__purchase-button--disabled">
                        売り切れ
                    </span>
                @endif
            </div>
            {{-- 商品説明 --}}
            <h2 class="item-show__section-title">商品説明</h2>
            <div class="item-show__description">
                {!! nl2br(e($item->description)) !!}
            </div>
            {{-- 商品の情報 --}}
            <h2 class="item-show__section-title">商品の情報</h2>
            <div class="item-show__info">
                {{-- カテゴリー --}}
                <div class="item-show__info-row">
                    <h3 class="item-show__info-label">カテゴリー</h3>
                    <div class="item-show__info-value">
                        @foreach ($item->categories_list as $category)
                            <span class="item-show__tag">{{ $category->name }}</span>
                        @endforeach
                    </div>
                </div>
                {{-- 商品の状態 --}}
                <div class="item-show__info-row">
                    <h3 class="item-show__info-label">商品の状態</h3>
                    <div class="item-show__info-value">
                        <span class="item-show__status">{{ $item->condition_label }}</span>
                    </div>
                </div>
            </div>
            {{-- コメント一覧 --}}
            <h2 class="item-show__section-title">コメント ({{ $item->comments->count() }})</h2>
            <div class="item-show__comments">
                @forelse ($item->comments as $comment)
                    <div class="item-show__comment">
                        {{-- アイコン＋ユーザー名 --}}
                        <div class="item-show__comment-header">
                            <div class="item-show__comment-icon">
                                @if ($comment->user->avatar)
                                    <img src="{{ $comment->user->image_url }}" alt="ユーザーアイコン">
                                @endif
                            </div>
                            <p class="item-show__comment-user">{{ $comment->user->name }}</p>
                        </div>
                        {{-- コメント本文 --}}
                        <p class="item-show__comment-body">{{ $comment->comment }}</p>
                    </div>
                @empty
                    <p class="item-show__no-comment">コメントはまだありません</p>
                @endforelse
            </div>
            {{-- コメント投稿フォーム --}}
            <h2 class="item-show__section-title">商品へのコメント</h2>
            @auth
                {{-- ログイン後：コメント投稿できる --}}
                <form action="{{ route('items.comment', $item->id) }}" method="POST" class="item-show__comment-form">
                @else
                    {{-- ログイン前：ログイン画面へ飛ばす --}}
                    <form action="{{ route('login') }}" method="GET" class="item-show__comment-form">
                    @endauth
                    @csrf
                    <textarea name="comment" class="item-show__comment-input">{{ old('comment') }}</textarea>
                    @auth
                        @error('comment')
                            <p class="item-show__error">{{ $message }}</p>
                        @enderror
                    @endauth
                    <button type="submit" class="item-show__comment-submit">
                        コメントを送信する
                    </button>
                </form>
        </div>
    </div>
@endsection
