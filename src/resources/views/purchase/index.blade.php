@extends('layouts.app', [
    'showSearch' => true,
    'authButtons' => 'auth',
])

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase/index.css') }}">
@endsection

@section('content')
    <div class="purchase">
        {{-- 商品情報 --}}
        <div class="purchase__left">
            {{-- 商品画像 + 商品名 + 価格--}}
            <div class="purchase__product">
                <div class="purchase__product-image">
                    <img src="{{ $item->image_url }}" alt="商品画像">
                </div>
                <div class="purchase__product-info">
                    <h2 class="purchase__title">{{ $item->title }}</h2>
                    <p class="purchase__price">¥{{ number_format($item->price) }}</p>
                </div>
            </div>
            {{-- 支払い方法 --}}
            <div class="purchase__section purchase__section--payment">
                <h3 class="purchase__section-title">支払い方法</h3>
                <form action="{{ route('purchase.updatePayment', $item->id) }}" method="POST">
                    @csrf
                    <select name="payment_method" class="purchase__select" onchange="this.form.submit()">
                        <option value="">選択してください</option>
                        <option value="convenience" {{ $payment_method === 'convenience' ? 'selected' : '' }}>
                            コンビニ支払い
                        </option>
                        <option value="card" {{ $payment_method === 'card' ? 'selected' : '' }}>
                            カード支払い
                        </option>
                    </select>
                </form>
            </div>
            {{-- 配送先 --}}
            <div class="purchase__section purchase__section--address purchase__section--address-bottom">
                <div class="purchase__section-header">
                    <h3 class="purchase__section-title">配送先</h3>
                    <a href="{{ route('purchase.address.edit', $item->id) }}" class="purchase__address-edit">
                        変更する
                    </a>
                </div>
                <p class="purchase__address">
                    〒 {{ $address->postal_code }}<br>
                    {{ $address->address }}<br>
                    {{ $address->building }}
                </p>
            </div>
        </div>
        {{-- 小計 --}}
        <div class="purchase__right">
            <div class="purchase__summary">
                <p class="purchase__summary-row purchase__summary-row--border">
                    <span>商品代金</span>
                    <span>¥{{ number_format($item->price) }}</span>
                </p>
                <p class="purchase__summary-row">
                    <span>支払い方法</span>
                    <span>
                        @if ($payment_method === 'convenience')
                            コンビニ払い
                        @elseif ($payment_method === 'card')
                            カード払い
                        @else
                            未選択
                        @endif
                    </span>
                </p>
            </div>
            {{-- 購入ボタンは枠の外 --}}
            <form action="{{ route('purchase.store', $item->id) }}" method="POST">
                @csrf
                <input type="hidden" name="payment_method" value="{{ $payment_method }}">
                <button type="submit" class="purchase__button">購入する</button>
            </form>
        </div>
    </div>
    </div>
@endsection
