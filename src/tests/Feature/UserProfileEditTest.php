<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserProfileEditTest extends TestCase
{
    use RefreshDatabase;

    /** プロフィール編集画面に初期値が正しく表示される */
    public function test_profile_edit_page_displays_initial_values()
    {
        // ユーザー作成
        $user = User::factory()->create([
            'name' => '福岡　太郎',
            'avatar' => 'avatar.png',
        ]);

        // 住所作成
        Address::factory()->create([
            'user_id' => $user->id,
            'postal_code' => '810-0001',
            'address' => '福岡市中央区天神2-2-2',
            'building' => '新ビル202',
        ]);

        // プロフィール編集画面へアクセス
        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->get('/mypage/profile/edit');

        // プロフィール画像
        $response->assertSee('avatar.png');

        // ユーザー名
        $response->assertSee('啓太');

        // 郵便番号
        $response->assertSee('810-0001');

        // 住所
        $response->assertSee('福岡市中央区天神2-2-2');

        // 建物名
        $response->assertSee('新ビル202');
    }
}