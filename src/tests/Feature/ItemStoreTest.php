<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemStoreTest extends TestCase
{
    use RefreshDatabase;

    /** 出品商品情報が正しく保存される */
    public function test_item_can_be_stored_correctly()
    {
        $user = User::factory()->create();

        // カテゴリを複数作成
        $cat1 = Category::factory()->create();
        $cat2 = Category::factory()->create();

        // 出品データ
        $postData = [
            'title' => 'ナイキ エアマックス',
            'brand' => 'NIKE',
            'description' => 'ほぼ未使用の美品です。',
            'price' => 12000,
            'condition' => 1,
            'category_ids' => [$cat1->id, $cat2->id],
        ];

        // 出品処理実行
        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->post('/items', $postData);

        // DB に保存されているか確認
        $this->assertDatabaseHas('items', [
            'title' => 'ナイキ エアマックス',
            'brand' => 'NIKE',
            'description' => 'ほぼ未使用の美品です。',
            'price' => 12000,
            'condition' => 1,
            'user_id' => $user->id,
        ]);

        // category_ids（JSON）が正しく保存されているか確認
        $item = Item::first();
        $this->assertEquals(
            json_encode([$cat1->id, $cat2->id]),
            $item->category_ids
        );

        // 正常にリダイレクトされる
        $response->assertRedirect('/items');
    }
}