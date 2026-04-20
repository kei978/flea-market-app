<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    /** いいねした商品だけが表示される */
    public function test_only_liked_items_are_displayed()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        /** @var \App\Models\User $user */
        $this->actingAs($user);

        // 商品を2つ作成
        $itemLiked = Item::factory()->create(['title' => 'Liked Item']);
        $itemNotLiked = Item::factory()->create(['title' => 'Not Liked Item']);

        // itemLiked だけいいね
        $itemLiked->likedUsers()->attach($user->id);

        $response = $this->get('/?tab=mylist');

        // いいねした商品は表示される
        $response->assertSee('Liked Item');

        // いいねしていない商品は表示されない
        $response->assertDontSee('Not Liked Item');
    }

    /** 購入済み商品は Sold と表示される */
    public function test_sold_items_show_sold_label_in_mylist()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        /** @var \App\Models\User $user */
        $this->actingAs($user);

        $soldItem = Item::factory()->create([
            'title' => 'Sold Item',
            'status' => 2,
        ]);

        $soldItem->likedUsers()->attach($user->id);

        $response = $this->get('/?tab=mylist');

        $response->assertSee('Sold');
        $response->assertSee('Sold Item');
    }

    /** 未認証は何も表示されない */
    public function test_guest_sees_no_items_in_mylist()
    {
        $response = $this->get('/?tab=mylist');

        // 商品が表示されないことを確認
        $response->assertDontSee('Sold');
    }
}