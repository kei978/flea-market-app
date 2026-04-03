<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    /** いいねした商品だけが表示される */
    public function test_only_liked_items_are_displayed()
    {
        $user = User::factory()->create();

        // いいねした商品
        $likedItem = Item::factory()->create();
        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        // いいねしていない商品
        $notLikedItem = Item::factory()->create();

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertSee($likedItem->title);
        $response->assertDontSee($notLikedItem->title);
    }

    /** 購入済み商品は Sold と表示される */
    public function test_sold_items_show_sold_label_in_mylist()
    {
        $user = User::factory()->create();

        $soldItem = Item::factory()->create(['status' => 2]);

        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $soldItem->id,
        ]);

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertSee('Sold');
    }

    /** 未ログインの場合は何も表示されない */
    public function test_guest_sees_no_items_in_mylist()
    {
        $item = Item::factory()->create();

        Like::factory()->create([
            'user_id' => 999, // 存在しないユーザー
            'item_id' => $item->id,
        ]);

        $response = $this->get('/?tab=mylist');

        // 商品名が表示されない
        $response->assertDontSee($item->title);

        // 空のメッセージが表示される（あなたの Blade に合わせて変更）
        $response->assertSee('商品がありません');
    }
}