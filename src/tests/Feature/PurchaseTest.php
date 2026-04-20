<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;


class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // verified ミドルウェアを無効化
        $this->withoutMiddleware(\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class);

        // payment_methods を作成
        \DB::table('payment_methods')->insert([
            ['id' => 1, 'name' => 'convenience'],
            ['id' => 2, 'name' => 'card'],
        ]);
    }

    public function test_user_can_start_purchase_process()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create(['status' => 1]);
        Address::factory()->create(['user_id' => $user->id]);

        /** @var \App\Models\User $user */
        $this->actingAs($user);

        $this->withSession([
            "payment_method_{$item->id}" => 'card',
        ]);

        $response = $this->post("/purchase/{$item->id}");

        $response->assertRedirect(route('purchase.success', ['item_id' => $item->id]));
    }

    public function test_item_becomes_sold_after_success()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create(['status' => 1]);
        Address::factory()->create(['user_id' => $user->id]);

        /** @var \App\Models\User $user */
        $this->actingAs($user);

        $this->withSession([
            "payment_method_{$item->id}" => 'card',
        ]);

        $this->get("/purchase/{$item->id}/success");

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 2,
        ]);
    }

    public function test_purchased_item_is_added_to_profile_purchase_list()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create(['status' => 1]);
        Address::factory()->create(['user_id' => $user->id]);

        /** @var \App\Models\User $user */
        $this->actingAs($user);

        $this->withSession([
            "payment_method_{$item->id}" => 'card',
        ]);

        $this->get("/purchase/{$item->id}/success");

        $this->assertDatabaseHas('orders', [
            'buyer_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}