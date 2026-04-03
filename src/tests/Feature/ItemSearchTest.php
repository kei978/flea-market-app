<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    /** 商品名で部分一致検索ができる */
    public function test_items_can_be_searched_by_partial_name()
    {
        // 部分一致する商品
        $item1 = Item::factory()->create(['title' => 'ナイキ スニーカー']);
        $item2 = Item::factory()->create(['title' => 'ナイキ ジャケット']);

        // 一致しない商品
        $item3 = Item::factory()->create(['title' => 'アディダス パーカー']);

        // 検索実行
        $response = $this->get('/?keyword=ナイキ');

        // 部分一致する商品は表示される
        $response->assertSee($item1->title);
        $response->assertSee($item2->title);

        // 一致しない商品は表示されない
        $response->assertDontSee($item3->title);
    }

    /** 検索状態がマイリストでも保持されている */
    public function test_search_keyword_is_kept_in_mylist()
    {
        $user = User::factory()->create();

        // いいねした商品
        $likedItem = Item::factory()->create(['title' => 'ナイキ スニーカー']);
        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        // いいねしていない商品
        $notLikedItem = Item::factory()->create(['title' => 'アディダス パーカー']);

        // ホームで検索 → マイリストへ遷移
        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->get('/?tab=mylist&keyword=ナイキ');

        // 検索キーワードが保持されている
        $response->assertSee('ナイキ');

        // いいね済み & 部分一致 → 表示される
        $response->assertSee($likedItem->title);

        // 一致しない商品は表示されない
        $response->assertDontSee($notLikedItem->title);
    }
}
