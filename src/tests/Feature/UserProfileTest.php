<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PaymentMethodSeeder::class);
    }

    /** プロフィール画面で必要な情報が取得できる */
    public function test_user_profile_displays_required_information()
    {
        // ユーザー作成
        $user = User::factory()->create([
            'name' => '福岡　太郎',
            'avatar' => 'avatar.png',
        ]);

        // 出品した商品
        $myItem1 = Item::factory()->create([
            'user_id' => $user->id,
            'title' => '出品商品A',
        ]);

        $myItem2 = Item::factory()->create([
            'user_id' => $user->id,
            'title' => '出品商品B',
        ]);

        // 購入した商品
        $purchasedItem = Item::factory()->create([
            'title' => '購入商品C',
        ]);

        Order::factory()->create([
            'buyer_id' => $user->id,
            'item_id' => $purchasedItem->id,
            'payment_method_id' => 2, // card
        ]);

        // マイページ（出品商品一覧）へアクセス
        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->get('/mypage?page=sell');

        // プロフィール画像
        $response->assertSee('avatar.png');

        // ユーザー名
        $response->assertSee('福岡　太郎');

        // 出品した商品一覧
        $response->assertSee('出品商品A');
        $response->assertSee('出品商品B');

        // 購入した商品一覧
        $responseBuy = $this->actingAs($user)->get('/mypage?page=buy');
        $responseBuy->assertSee('購入商品C');
    }
}