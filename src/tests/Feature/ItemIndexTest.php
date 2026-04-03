<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    /** 全商品が取得できる */
    public function test_all_items_are_displayed()
    {
        $items = Item::factory()->count(3)->create();

        $response = $this->get('/?tab=recommend');

        foreach ($items as $item) {
            $response->assertSee($item->title);
        }
    }

    /** 購入済み商品は Sold と表示される */
    public function test_sold_items_show_sold_label()
    {
        $item = Item::factory()->create(['status' => 2]); // 2 = sold

        $response = $this->get('/?tab=recommend');

        $response->assertSee('Sold');
    }

    /** 自分が出品した商品は表示されない */
    public function test_user_own_items_are_not_displayed()
    {
        $user = User::factory()->create();

        // 自分の商品
        $myItem = Item::factory()->create([
            'user_id' => $user->id,
        ]);

        // 他人の商品
        $otherItem = Item::factory()->create();

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->get('/?tab=recommend');

        // 自分の商品は見えない
        $response->assertDontSee($myItem->title);

        // 他人の商品は見える
        $response->assertSee($otherItem->title);
    }
}