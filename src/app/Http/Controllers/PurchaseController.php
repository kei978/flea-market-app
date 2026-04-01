<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    /**
     * 購入画面表示
     */
    public function index($id)
    {
        $item = Item::findOrFail($id);

        // ログインユーザーの住所
        $address = Address::where('user_id', Auth::id())->first();

        // セッションから支払い方法を取得（初期値は null）
        $payment_method = session()->get("payment_method_{$id}");

        return view('purchase.index', compact('item', 'address', 'payment_method'));
    }

    /**
     * 支払い方法更新（プルダウン変更時に自動反映）
     */
    public function updatePayment(Request $request, $id)
    {
        $request->validate([
            'payment_method' => ['required', 'in:convenience,card'],
        ]);

        // セッションに保存
        session()->put("payment_method_{$id}", $request->payment_method);

        return back();
    }

    /**
     * 購入処理
     */
    public function store(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        // すでに売り切れなら購入不可
        if ($item->status == 2) {
            return redirect()->route('items.index')->with('error', 'この商品はすでに売り切れています');
        }

        // セッションから支払い方法を取得
        $payment_method = session()->get("payment_method_{$id}");
        if (!$payment_method) {
            return back()->with('error', '支払い方法を選択してください');
        }

        // 🔥 ここで payment_method_id に変換する
        $payment_method_id = $payment_method === 'convenience' ? 1 : 2;

        // 住所取得
        $address = Address::where('user_id', Auth::id())->first();
        if (!$address) {
            return back()->with('error', '配送先住所が登録されていません');
        }

        // 注文作成
        Order::create([
            'buyer_id' => Auth::id(),
            'item_id' => $item->id,
            'payment_method_id' => $payment_method_id, // ← これが必須
            'price' => $item->price,
            'status' => 1, // 購入済みなどのステータス
        ]);

        // 商品を sold 状態に更新
        $item->status = 2;
        $item->save();

        // セッションの支払い方法を削除
        session()->forget("payment_method_{$id}");

        return redirect()->route('items.index')->with('success', '購入が完了しました');
    }


    public function editAddress($item_id)
    {
        $item = Item::findOrFail($item_id);

        $user = Auth::user();
        $address = Address::where('user_id', $user->id)->first();

        return view('profile.address', compact('item', 'user', 'address')); // ★ user を追加
    }



    public function updateAddress(Request $request, $item_id)
    {
        $request->validate([
            'postal_code' => 'required',
            'address' => 'required',
            'building' => 'nullable',
        ]);

        $address = Address::where('user_id', Auth::id())->first();
        $address->update([
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'building' => $request->building,
        ]);

        return redirect()->route('purchase.index', $item_id)
            ->with('success', '住所を更新しました');
    }
}