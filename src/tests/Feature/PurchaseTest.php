<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /** 購入処理が実行される（Stripe モック） */
    public function test_user_can_start_purchase_process()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 1]);
        Address::factory()->create(['user_id' => $user->id]);

        // 支払い方法を session に保存
        session()->put("payment_method_{$item->id}", 'card');

        // StripeSession モック
        $mock = Mockery::mock('alias:Stripe\Checkout\Session');
        $mock->shouldReceive('create')
            ->once()
            ->andReturn((object)['url' => 'https://stripe.test/checkout']);

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->post("/purchase/{$item->id}");

        // Stripe の Checkout URL にリダイレクトされる
        $response->assertRedirect('https://stripe.test/checkout');
    }

    /** 購入後、商品が sold になる */
    public function test_item_becomes_sold_after_success()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 1]);
        Address::factory()->create(['user_id' => $user->id]);

        // 支払い方法を session に保存
        session()->put("payment_method_{$item->id}", 'card');

        // success 処理を実行
        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->get("/purchase/{$item->id}/success");

        // 商品が sold（status=2）になっている
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 2,
        ]);

        $response->assertRedirect('/items');
    }

    /** 購入した商品がプロフィールの購入一覧に追加される */
    public function test_purchased_item_is_added_to_profile_purchase_list()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 1]);
        Address::factory()->create(['user_id' => $user->id]);

        session()->put("payment_method_{$item->id}", 'card');

        // success 処理を実行
        /** @var \App\Models\User $user */
        $this->actingAs($user)->get("/purchase/{$item->id}/success");

        // orders テーブルにレコードが追加されている
        $this->assertDatabaseHas('orders', [
            'buyer_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
