<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemStoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function item_can_be_stored_correctly()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        // カテゴリ作成
        $cat1 = Category::factory()->create(['name' => 'メンズ']);
        $cat2 = Category::factory()->create(['name' => 'スニーカー']);

        // POST データ
        $postData = [
            'title' => 'ナイキ エアマックス',
            'brand' => 'NIKE',
            'description' => 'ほぼ未使用の美品です。',
            'price' => 12000,
            'condition' => 1,
            'categories' => [$cat1->id, $cat2->id],
            'image' => UploadedFile::fake()->create('test.jpg', 100),
        ];

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->post('/sell', $postData);

        // 保存されているか確認
        $this->assertDatabaseHas('items', [
            'title' => 'ナイキ エアマックス',
            'brand' => 'NIKE',
            'description' => 'ほぼ未使用の美品です。',
            'price' => 12000,
            'condition' => 1,
            'user_id' => $user->id,
        ]);

        // 画像が保存されたか
        $item = \App\Models\Item::first();
        Storage::disk('public')->assertExists($item->image_path);

        $response->assertStatus(302);
    }
}