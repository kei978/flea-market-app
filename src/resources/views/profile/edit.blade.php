@extends('layouts.app', [
    'showSearch' => true,
    'authButtons' => 'auth',
])

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile/edit.css') }}">
@endsection

@section('content')
    <div class="card">
        <div class="card__title">
            <h1>プロフィール設定</h1>
        </div>
        <form action="{{ route('mypage.update') }}" class="form" method="POST" enctype="multipart/form-data">
            @csrf
            {{-- プロフィール画像 --}}
            <div class="form__avatar">
                <label for="avatar" class="form__avatar-image">
                    @if ($user->avatar)
                        <img id="avatarPreview" src="{{ $user->image_url }}?v={{ time() }}" alt="">
                    @endif
                </label>
                <label for="avatar" class="form__avatar-label">
                    画像を選択する
                </label>
                <input type="file" id="avatar" name="avatar" accept="image/*" class="form__avatar-input">
                <div class="form__error">
                    @error('avatar')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            {{-- JS（プレビュー用） --}}
            <script>
                document.getElementById('avatar').addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = function(event) {
                        document.getElementById('avatarPreview').src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            </script>
            {{-- ユーザー名 --}}
            <div class="form__group">
                <div class="form__group-title">
                    <h2 class="form__label">ユーザー名</h2>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                            class="form__input--text-input">
                    </div>
                    <div class="form__error">
                        @error('name')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            {{-- 郵便番号 --}}
            <div class="form__group">
                <div class="form__group-title">
                    <h2 class="form__label">郵便番号</h2>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="postal_code"
                            value="{{ old('postal_code', $address->postal_code ?? '') }}" class="form__input--text-input">
                    </div>
                    <div class="form__error">
                        @error('postal_code')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            {{-- 住所 --}}
            <div class="form__group">
                <div class="form__group-title">
                    <h2 class="form__label">住所</h2>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="address" value="{{ old('address', $address->address ?? '') }}"
                            class="form__input--text-input">
                    </div>
                    <div class="form__error">
                        @error('address')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            {{-- 建物名 --}}
            <div class="form__group">
                <div class="form__group-title">
                    <h2 class="form__label">建物名</h2>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="building" value="{{ old('building', $address->building ?? '') }}"
                            class="form__input--text-input">
                    </div>
                    <div class="form__error">
                        @error('building')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            {{-- 更新ボタン --}}
            <div class="form__button">
                <button class="form__button-submit" type="submit">更新する</button>
            </div>
        </form>
    </div>
@endsection
