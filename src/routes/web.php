<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ExhibitionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| 公開ページ（認証不要）
|--------------------------------------------------------------------------
*/

// トップページ（商品一覧）
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// 商品詳細
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');

/*
|--------------------------------------------------------------------------
| メール認証完了時の遷移（ここに追加）
|--------------------------------------------------------------------------
*/
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/mypage/profile');
})->middleware(['auth', 'signed'])->name('verification.verify');


/*
|--------------------------------------------------------------------------
| 認証必須ページ（ログイン済み + メール認証済み）
|--------------------------------------------------------------------------
|
| Fortify のメール認証を正しく発動させるためには、
| 「auth」だけでなく「verified」も必ず付ける必要がある。
|
*/
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/mypage', [ProfileController::class, 'profile'])->name('mypage.index');

    // プロフィール編集
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('mypage.update');

    // マイリスト
    Route::get('/mylist', [ItemController::class, 'index'])->name('items.mylist');

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
    Route::get('/purchase/{item_id}/address', [PurchaseController::class, 'editAddress'])
        ->name('purchase.address.edit');
    Route::post('/purchase/{item_id}/address/update', [PurchaseController::class, 'updateAddress'])
        ->name('purchase.address.update');

    // いいね
    Route::post('/items/{id}/like', [ItemController::class, 'like'])->name('items.like');

    // コメント
    Route::post('/items/{id}/comment', [ItemController::class, 'comment'])->name('items.comment');

    // 支払い方法更新
    Route::post('/purchase/{item_id}/payment', [PurchaseController::class, 'updatePayment'])
        ->name('purchase.updatePayment');


});