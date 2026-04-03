<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemShowTest extends TestCase
{
    use RefreshDatabase;

    /** 商品詳細ページに必要な情報が表示される */
    public function test_item_detail_page_displays_all_required_information()
    {
        $user = User::factory()->create();

        // カテゴリを複数作成
        $cat1 = Category::factory()->create(['name' => 'メンズ']);
        $cat2 = Category::factory()->create(['name' => 'スニーカー']);

        // 商品作成（category_ids は JSON 配列）
        $item = Item::factory()->create([
            'title' => 'ナイキ エアマックス',
            'brand' => 'NIKE',
            'price' => 12000,
            'description' => "とても綺麗な状態です。\nほぼ未使用。",
            'condition' => 1,
            'category_ids' => json_encode([$cat1->id, $cat2->id]),
        ]);

        // いいね数
        Like::factory()->count(3)->create([
            'item_id' => $item->id,
        ]);

        // コメント
        Comment::factory()->create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'comment' => 'とても良い商品ですね！',
        ]);

        $response = $this->get("/item/{$item->id}");

        // 商品基本情報
        $response->assertSee('ナイキ エアマックス');
        $response->assertSee('NIKE');
        $response->assertSee('12000');
        $response->assertSee('とても綺麗な状態です。');
        $response->assertSee('ほぼ未使用。');

        // カテゴリ（複数）
        $response->assertSee('メンズ');
        $response->assertSee('スニーカー');

        // 商品状態（あなたのアプリの表示に合わせて変更可能）
        $response->assertSee('新品'); // condition=1 の場合の表示

        // いいね数
        $response->assertSee('3');

        // コメント数
        $response->assertSee('1');

        // コメント内容
        $response->assertSee('とても良い商品ですね！');
        $response->assertSee($user->name);
    }

    /** 複数カテゴリが正しく表示される */
    public function test_multiple_categories_are_displayed()
    {
        $cat1 = Category::factory()->create(['name' => 'メンズ']);
        $cat2 = Category::factory()->create(['name' => 'スニーカー']);

        $item = Item::factory()->create([
            'category_ids' => json_encode([$cat1->id, $cat2->id]),
        ]);

        $response = $this->get("/item/{$item->id}");

        $response->assertSee('メンズ');
        $response->assertSee('スニーカー');
    }
}
