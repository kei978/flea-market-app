@extends('layouts.app', [
    'showSearch' => true,
    'authButtons' => 'auth',
])

@section('css')
    <link rel="stylesheet" href="{{ asset('css/items/create.css') }}">
@endsection

@section('content')
    <div class="item-create">
        <h1 class="item-create__title">商品の出品</h1>
        <form action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data" class="item-create__form">
            @csrf
            {{-- 商品画像 --}}
            <h3 class="form__label">商品画像</h3>
            <div class="form__group">
                <div class="form__image-frame">
                    <img id="preview" class="form__image-preview" style="display:none;">
                    <label for="image" class="form__image-button">画像を選択する</label>
                    <input type="file" id="image" name="image" accept="image/*" class="form__image-input">
                </div>
                <div class="form__error">
                    @error('image')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            {{-- 商品の詳細 --}}
            <h2 class="form__section-title">商品の詳細</h2>
            {{-- カテゴリー --}}
            <h3 class="form__label">カテゴリー</h3>
            <div class="form__group">
                <div class="form__tag-list">
                    @foreach ($categories as $category)
                        <label class="form__tag-item">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                            <span>{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="form__error">
                    @error('categories')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            {{-- 商品の状態 --}}
            <h3 class="form__label">商品の状態</h3>
            <div class="form__group">
                <select name="condition" class="form__select">
                    <option value="">選択してください</option>
                    <option value="1" {{ old('condition') == 1 ? 'selected' : '' }}>良好</option>
                    <option value="2" {{ old('condition') == 2 ? 'selected' : '' }}>目立った傷や汚れなし</option>
                    <option value="3" {{ old('condition') == 3 ? 'selected' : '' }}>やや傷や汚れあり</option>
                    <option value="4" {{ old('condition') == 4 ? 'selected' : '' }}>状態が悪い</option>
                </select>
                <div class="form__error">
                    @error('condition')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            {{-- 商品名と説明 --}}
            <h2 class="form__section-title">商品名と説明</h2>
            {{-- 商品名 --}}
            <h3 class="form__label">商品名</h3>
            <div class="form__group">
                <input type="text" name="title" value="{{ old('title') }}" class="form__input">
                <div class="form__error">
                    @error('title')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            {{-- ブランド名 --}}
            <h3 class="form__label">ブランド名</h3>
            <div class="form__group">
                <input type="text" name="brand" value="{{ old('brand') }}" class="form__input">
                <div class="form__error">
                    @error('brand')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            {{-- 商品の説明 --}}
            <h3 class="form__label">商品の説明</h3>
            <div class="form__group">
                <textarea name="description" class="form__textarea--inputlike">{{ old('description') }}</textarea>
                <div class="form__error">
                    @error('description')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            {{-- 販売価格 --}}
            <h3 class="form__label">販売価格</h3>
            <div class="form__group">
                <div class="form__price-wrapper">
                    <input type="text" id="price" name="price" value="{{ old('price') }}" class="form__input">
                </div>
                <div class="form__error">
                    @error('price')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            {{-- 出品ボタン --}}
            <div class="form__button">
                <button type="submit" class="form__button-submit">出品する</button>
            </div>
        </form>
    </div>

    {{-- 販売価格カンマ自動挿入 --}}
    <script>
        const priceInput = document.getElementById('price');
        // フォーカスしたらカンマを外す
        priceInput.addEventListener('focus', function(e) {
            let value = e.target.value;
            value = value.replace(/,/g, ''); // カンマ除去
            e.target.value = value;
        });
        // フォーカスが外れたらカンマを付ける
        priceInput.addEventListener('blur', function(e) {
            let value = e.target.value;
            // 数字以外を除去
            value = value.replace(/[^0-9]/g, '');
            // 空なら何もしない
            if (value === '') return;
            // カンマ付け
            value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            e.target.value = value;
        });
    </script>

    {{-- 画像プレビュー用スクリプト --}}
    <script>
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            const preview = document.getElementById('preview');
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        });
    </script>
@endsection
