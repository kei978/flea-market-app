<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /** ログイン済みユーザーはコメントを送信できる */
    public function test_authenticated_user_can_post_comment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", [
            'comment' => 'とても良い商品ですね！',
        ]);

        // コメントが保存されている
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'とても良い商品ですね！',
        ]);

        $response->assertRedirect(); // 遷移先はあなたのアプリに合わせて変更
    }

    /** 未ログインユーザーはコメントを送信できない */
    public function test_guest_cannot_post_comment()
    {
        $item = Item::factory()->create();

        $response = $this->post("/item/{$item->id}/comment", [
            'comment' => 'ゲストコメント',
        ]);

        // コメントは保存されない
        $this->assertDatabaseMissing('comments', [
            'comment' => 'ゲストコメント',
        ]);

        // ログイン画面へリダイレクト
        $response->assertRedirect('/login');
    }

    /** コメント未入力はバリデーションエラー */
    public function test_comment_is_required()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", [
            'comment' => '',
        ]);

        $response->assertSessionHasErrors([
            'comment' => 'コメントを入力してください',
        ]);
    }

    /** コメントが255文字以上はバリデーションエラー */
    public function test_comment_must_not_exceed_255_characters()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $longComment = str_repeat('あ', 256);

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", [
            'comment' => $longComment,
        ]);

        $response->assertSessionHasErrors([
            'comment' => 'コメントは255文字以内で入力してください',
        ]);
    }
}