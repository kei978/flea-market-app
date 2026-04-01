<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ExhibitionController;
use App\Http\Controllers\ProfileController;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| 公開ページ（認証不要）
|--------------------------------------------------------------------------
*/

// 商品一覧（トップ画面）
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// 商品詳細
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');

Route::post('/login', function (LoginRequest $request) {

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        // 🔥 未認証ならメール認証メールを再送して誘導画面へ
        if (!Auth::user()->hasVerifiedEmail()) {

            // 認証メールを再送
            Auth::user()->sendEmailVerificationNotification();

            return redirect()->route('verification.notice');
        }

        return redirect()->intended('/');
    }

    return back()->withErrors([
        'email' => 'ログイン情報が登録されていません',
    ]);
})->name('login');


/*
|--------------------------------------------------------------------------
| 認証必須ページ（Fortify の auth + verified）
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // 商品一覧（マイリスト）
    Route::get('/?tab=mylist', [ItemController::class, 'index'])->name('items.mylist');

    // 商品出品
    Route::get('/sell', [ExhibitionController::class, 'create'])->name('sell.index');
    Route::post('/sell', [ExhibitionController::class, 'store'])->name('sell.store');

    // 商品購入
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'index'])->name('purchase.index');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.store');

    // 購入画面の住所変更
    Route::get('/purchase/{item_id}/address', [PurchaseController::class, 'editAddress'])
        ->name('purchase.address.edit');
    Route::post('/purchase/{item_id}/address/update', [PurchaseController::class, 'updateAddress'])
        ->name('purchase.address.update');

    // マイページ
    Route::get('/mypage', [ProfileController::class, 'profile'])->name('mypage.index');

    // プロフィール編集（1画面・1更新処理）
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('mypage.update');

    // プロフィール編集（1画面・1更新処理）
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('mypage.update');

    // いいね
    Route::post('/items/{id}/like', [ItemController::class, 'like'])->name('items.like');

    // コメント
    Route::post('/items/{id}/comment', [ItemController::class, 'comment'])->name('items.comment');

    Route::post('/purchase/{item_id}/payment', [PurchaseController::class, 'updatePayment'])
        ->name('purchase.updatePayment');
});