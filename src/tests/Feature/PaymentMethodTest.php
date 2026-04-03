<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    /** 支払い方法を選択すると小計画面に反映される */
    public function test_selected_payment_method_is_reflected_in_purchase_page()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        Address::factory()->create(['user_id' => $user->id]);

        // 1. 支払い方法を選択（updatePayment）
        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->post("/purchase/{$item->id}/payment", [
            'payment_method' => 'card',
        ]);

        $response->assertRedirect(); // 元の画面に戻る

        // セッションに保存されていることを確認
        $this->assertEquals('card', session()->get("payment_method_{$item->id}"));

        // 2. 購入画面を開く
        $response = $this->actingAs($user)->get("/purchase/{$item->id}");

        // 画面に選択した支払い方法が反映されている
        $response->assertSee('card'); // Blade の表示に合わせて変更可能
    }
}
