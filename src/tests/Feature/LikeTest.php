<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    /** いいね登録ができる */
    public function test_user_can_like_an_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->post("/item/{$item->id}/like");

        // DB にレコードが作成されている
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // いいね数が増えている
        $this->assertEquals(1, Like::where('item_id', $item->id)->count());

        $response->assertStatus(200);
    }

    /** いいね済みアイコンが色変化（＝いいね済み状態が返る） */
    public function test_liked_item_returns_liked_state()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->get("/item/{$item->id}");

        // Blade 側で「いいね済み」を示すクラスや文言を確認
        $response->assertSee('liked'); // あなたの Blade に合わせて変更
    }

    /** いいね解除ができる */
    public function test_user_can_unlike_an_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 事前にいいねしておく
        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->delete("/item/{$item->id}/like");

        // DB からレコードが削除されている
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // いいね数が減っている
        $this->assertEquals(0, Like::where('item_id', $item->id)->count());

        $response->assertStatus(200);
    }
}
