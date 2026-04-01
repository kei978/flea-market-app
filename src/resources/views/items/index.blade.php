@extends('layouts.app', [
    'showSearch' => true,
    'authButtons' => Auth::check() ? 'auth' : 'guest',
])

@section('css')
    <link rel="stylesheet" href="{{ asset('css/items/index.css') }}">
@endsection

@section('content')
    @php
        use Illuminate\Support\Str;
    @endphp

    <div class="items">
        {{-- タブ --}}
        <div class="items__tabs">
            <a href="{{ route('items.index', ['tab' => 'recommend', 'keyword' => request('keyword')]) }}"
                class="items__tab {{ ($tab ?? 'recommend') === 'recommend' ? 'active' : '' }}">
                おすすめ
            </a>
            <a href="{{ route('items.index', ['tab' => 'mylist', 'keyword' => request('keyword')]) }}"
                class="items__tab {{ ($tab ?? '') === 'mylist' ? 'active' : '' }}">
                マイリスト
            </a>
        </div>
        {{-- 商品一覧 --}}
        <div class="items__list">
            @forelse ($items as $item)
                <a href="{{ route('items.show', $item->id) }}" class="items__card">
                    {{-- 商品画像 --}}
                    <div class="items__image">
                        <img src="{{ $item->image_url }}" alt="商品画像">
                        @if ($item->status == 2)
                            <span class="items__sold">Sold</span>
                        @endif
                    </div>
                    {{-- 商品名 --}}
                    <div class="items__name">
                        {{ $item->title }}
                    </div>
                </a>
            @empty
                <p class="items__empty">商品がありません</p>
            @endforelse
        </div>
    </div>
@endsection
