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

    /** いいねできる */
    public function test_user_can_like_an_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->post("/item/{$item->id}/like");

        // DB に保存されている
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // リダイレクト（302）が正しい
        $response->assertStatus(302);
    }

    /** いいね済み状態が表示される（ハート画像で判定） */
    public function test_liked_item_returns_liked_state()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->get("/item/{$item->id}");

        // ピンクのハート画像が表示されているかで判定
        $response->assertSee('ハートロゴ_ピンク.png');
    }

    /** いいね解除できる */
    public function test_user_can_unlike_an_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->post("/item/{$item->id}/like");

        // DB から削除されている
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response->assertStatus(302);
    }
}
