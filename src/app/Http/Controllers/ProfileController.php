<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    // マイページ表示（/mypage）を追加
    public function profile(Request $request)
    {
        $user = Auth::user();
        $address = Address::where('user_id', $user->id)->first();

        // page パラメータ（sell / buy）
        $page = $request->query('page', 'sell');

        // タブごとに表示する商品を切り替え
        if ($page === 'sell') {
            // 出品した商品
            $items = Item::where('user_id', $user->id)->get();
        } else {
            // 購入した商品
            $items = Item::whereHas('orders', function ($q) use ($user) {
                $q->where('buyer_id', $user->id);
            })->get();
        }

        return view('profile.index', compact('user', 'address', 'page', 'items'));
    }

    // プロフィール編集画面
    public function edit()
    {
        $user = Auth::user();

        // 住所レコードがなければ自動作成
        $address = Address::firstOrCreate(
            ['user_id' => $user->id],
            ['postal_code' => '', 'address' => '', 'building' => '']
        );

        return view('profile.edit', compact('user', 'address'));
    }

    // プロフィール更新処理
    public function update(ProfileRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 住所レコード取得（なければ作成）
        $address = Address::firstOrCreate(
            ['user_id' => $user->id],
            ['postal_code' => '', 'address' => '', 'building' => '']
        );

        // プロフィール画像
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        // ユーザー名
        $user->name = $request->name;
        $user->save();

        // 住所情報
        $address->update([
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'building' => $request->building,
        ]);

        return redirect()
            ->route('items.index')
            ->with('success', 'プロフィールを更新しました');
    }
}