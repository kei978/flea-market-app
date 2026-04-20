<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ExhibitionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

/* 公開ページ（認証不要） */
// トップページ（商品一覧）
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// 商品詳細
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');

Route::post('/login', function (LoginRequest $request) {

    $credentials = $request->only('email', 'password');

    if (! Auth::attempt($credentials)) {
        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }

    /** @var \App\Models\User $user */
    $user = Auth::user();

    // 未認証ユーザー → メール認証画面へ
    if (! $user->hasVerifiedEmail()) {
        $user->sendEmailVerificationNotification();
        return redirect()->route('verification.notice');
    }

    // 認証済みユーザー → intended
    return redirect()->intended('/?tab=mylist');
})->name('login');


// メール認証完了時の遷移
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/mypage/profile');
})->middleware(['auth', 'signed'])->name('verification.verify');

/* 認証必須ページ（ログイン済み + メール認証済み） */
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/mypage', [ProfileController::class, 'profile'])->name('mypage.index');

    // プロフィール編集
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('mypage.update');

    // 商品出品
    Route::get('/sell', [ExhibitionController::class, 'create'])->name('sell.index');
    Route::post('/sell', [ExhibitionController::class, 'store'])->name('sell.store');

    // 支払い方法更新
    Route::get('/purchase/{item_id}/success', [PurchaseController::class, 'success'])
        ->name('purchase.success');

    // 商品購入
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'index'])->name('purchase.index');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.store');

    // 購入画面の住所変更
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])
        ->name('purchase.address.edit');
    Route::post('/purchase/address/{item_id}/update', [PurchaseController::class, 'updateAddress'])
        ->name('purchase.address.update');

    // いいね
    Route::post('/item/{id}/like', [ItemController::class, 'like'])->name('items.like');

    // コメント
    Route::post('/item/{id}/comment', [ItemController::class, 'comment'])->name('items.comment');

    // 支払い方法更新
    Route::post('/purchase/{item_id}/payment', [PurchaseController::class, 'updatePayment'])
        ->name('purchase.updatePayment');
});