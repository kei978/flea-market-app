<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;


class PurchaseController extends Controller
{
    /* 購入画面表示 */
    public function index($item_id)
    {
        $item = Item::findOrFail($item_id);

        // ★ セッション住所があれば優先
        $sessionAddress = session()->get("purchase_address_{$item_id}");

        if ($sessionAddress) {
            $address = (object) $sessionAddress;
        } else {
            $address = Address::where('user_id', Auth::id())->first();
        }

        $payment_method = session()->get("payment_method_{$item_id}");

        return view('purchase.index', compact('item', 'address', 'payment_method'));
    }

    /* 支払い方法更新 */
    public function updatePayment(Request $request, $id)
    {
        $request->validate([
            'payment_method' => ['required', 'in:convenience,card'],
        ]);

        session()->put("payment_method_{$id}", $request->payment_method);

        return back();
    }

    /* Stripe Checkout へ遷移 */
    public function store(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        // 売り切れチェック
        if ($item->status == 2) {
            return redirect()->route('items.index')
                ->with('error', 'この商品はすでに売り切れています');
        }

        // 支払い方法チェック
        $payment_method = session()->get("payment_method_{$id}");
        if (!$payment_method) {
            return back()->with('error', '支払い方法を選択してください');
        }

        // 住所チェック
        $address = Address::where('user_id', Auth::id())->first();
        if (!$address) {
            return back()->with('error', '配送先住所が登録されていません');
        }

        /* テスト */
        if (app()->runningUnitTests()) {
            return redirect()->route('purchase.success', ['item_id' => $id]);
        }

        /* 本番 */
        Stripe::setApiKey(config('services.stripe.secret'));

        $lineItems = [[
            'price_data' => [
                'currency' => 'jpy',
                'product_data' => [
                    'name' => $item->title,
                ],
                'unit_amount' => $item->price,
            ],
            'quantity' => 1,
        ]];

        if ($payment_method === 'card') {
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('purchase.success', ['item_id' => $item->id]),
                'cancel_url'  => route('purchase.index',   ['item_id' => $item->id]),
            ]);
        } elseif ($payment_method === 'convenience') {
            $session = StripeSession::create([
                'payment_method_types' => ['konbini'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('purchase.success', ['item_id' => $item->id]),
                'cancel_url'  => route('purchase.index',   ['item_id' => $item->id]),
            ]);
        }

        return redirect($session->url);
    }

    /* Stripe 決済成功後の処理 */
    public function success($item_id)
    {
        $item = Item::findOrFail($item_id);

        // 支払い方法
        $payment_method = session()->get("payment_method_{$item_id}", 'card');
        $payment_method_id = $payment_method === 'convenience' ? 1 : 2;

        // Order作成
        $order = Order::firstOrCreate(
            [
                'buyer_id' => Auth::id(),
                'item_id'  => $item->id,
            ],
            [
                'payment_method_id' => $payment_method_id,
                'price' => $item->price,
                'status' => 1,
            ]
        );

        // セッション住所があれば優先
        $sessionAddress = session()->get("purchase_address_{$item_id}");

        if ($sessionAddress) {
            $addressData = $sessionAddress;
        } else {
            $address = Address::where('user_id', Auth::id())->first();
            $addressData = [
                'postal_code' => $address->postal_code,
                'address'     => $address->address,
                'building'    => $address->building,
            ];
        }

        // order_addresses に保存
        $order->orderAddress()->firstOrCreate([], $addressData);

        // 商品を sold に
        $item->update(['status' => 2]);

        // ★ セッション削除
        session()->forget("payment_method_{$item_id}");
        session()->forget("purchase_address_{$item_id}");

        return redirect()->route('items.index')->with('success', '購入が完了しました');
    }

    public function editAddress($item_id)
    {
        $item = Item::findOrFail($item_id);

        $user = Auth::user();
        $address = Address::where('user_id', $user->id)->first();

        return view('profile.address', compact('item', 'user', 'address'));
    }

    public function updateAddress(Request $request, $item_id)
    {
        $request->validate([
            'postal_code' => 'required',
            'address' => 'required',
            'building' => 'nullable',
        ]);

        session()->put("purchase_address_{$item_id}", [
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'building' => $request->building,
        ]);

        return redirect()->route('purchase.index', $item_id)
            ->with('success', '住所を更新しました');
    }
}