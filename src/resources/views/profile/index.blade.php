@extends('layouts.app', [
    'showSearch' => true,
    'authButtons' => 'auth',
])

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile/index.css') }}">
@endsection

@section('content')
    <div class="profile">

        <div class="profile__header">
            <div class="profile__avatar">
                @if ($user->avatar)
                    <img src="{{ $user->image_url }}" alt="プロフィール画像">
                @endif
            </div>

            <h2 class="profile__name">{{ $user->name }}</h2>

            <a href="{{ route('mypage.edit') }}" class="profile__edit-button">
                プロフィールを編集
            </a>
        </div>


        {{-- タブ --}}
        <div class="profile__tabs">
            <a href="{{ route('mypage.index', ['page' => 'sell']) }}"
                class="profile__tab {{ $page === 'sell' ? 'active' : '' }}">
                出品した商品
            </a>

            <a href="{{ route('mypage.index', ['page' => 'buy']) }}"
                class="profile__tab {{ $page === 'buy' ? 'active' : '' }}">
                購入した商品
            </a>
        </div>

        {{-- 商品一覧 --}}
        <div class="profile__items">
            @forelse ($items as $item)
                <a href="{{ route('items.show', $item->id) }}" class="profile__item-card">
                    <div class="profile__item-image">
                        <img src="{{ $item->image_url }}" alt="商品画像">

                        @if ($item->status == 2)
                            <span class="profile__item-sold">Sold</span>
                        @endif
                    </div>

                    <div class="profile__item-name">
                        {{ $item->title }}
                    </div>

                </a>
            @empty
                <p class="profile__empty">商品がありません</p>
            @endforelse
        </div>

    </div>
@endsection
