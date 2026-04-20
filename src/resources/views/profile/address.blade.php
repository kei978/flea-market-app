@extends('layouts.app', [
    'showSearch' => true,
    'authButtons' => 'auth'
])

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile/address.css') }}">
@endsection

@section('content')
<div class="profile-address">

    <h1 class="profile-address__title">住所の変更</h1>

    <form action="{{ route('purchase.address.update', $item->id) }}" method="POST" class="profile-address__form">
        @csrf

        {{-- 郵便番号 --}}
        <div class="form__group">
            <h2 class="form__label">郵便番号</h2>
            <input type="text" name="postal_code" value="{{ old('postal_code', $address->postal_code) }}" class="form__input">
            @error('postal_code')
                <p class="form__error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 住所 --}}
        <div class="form__group">
            <h2 class="form__label">住所</h2>
            <input type="text" name="address" value="{{ old('address', $address->address) }}" class="form__input">
            @error('address')
                <p class="form__error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 建物名 --}}
        <div class="form__group">
            <h2 class="form__label">建物名</h2>
            <input type="text" name="building" value="{{ old('building', $address->building) }}" class="form__input">
            @error('building')
                <p class="form__error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="profile-address__button">更新する</button>

    </form>

</div>
@endsection
