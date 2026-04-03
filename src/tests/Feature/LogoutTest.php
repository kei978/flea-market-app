<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** ログアウトができる */
    public function test_user_can_logout_successfully()
    {
        // 1. ユーザー作成 & ログイン
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. ログアウト実行（Fortify は POST /logout）
        $response = $this->post('/logout');

        // 3. 認証解除されていることを確認
        $this->assertGuest();

        // 4. 遷移先の確認（あなたのアプリに合わせて変更）
        $response->assertRedirect('/');
    }
}