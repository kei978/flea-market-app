<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;


class AddressUpdateTest extends TestCase
{
    /** 購入画面に更新後の住所が反映される */
    public function test_updated_address_is_reflected_in_purchase_page()
    {

        $this->withoutMiddleware();

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        Address::factory()->create([
            'user_id' => $user->id,
        ]);

        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);


        /** @var \App\Models\User $user */
        $this->actingAs($user);

        $this->post("/purchase/{$item->id}/address/update", [
            'postal_code' => '810-0001',
            'address'     => '福岡市中央区天神2-2-2',
            'building'    => '新ビル202',
        ]);

        $response = $this->get("/purchase/{$item->id}");

        $response->assertSee('810-0001');
        $response->assertSee('福岡市中央区天神2-2-2');
        $response->assertSee('新ビル202');
    }

    /** 注文作成時に正しい住所が紐づく */
    public function test_purchased_item_has_correct_address()
    {
        $this->withoutMiddleware();

        DB::table('payment_methods')->insert([
            ['id' => 1, 'name' => 'convenience'],
            ['id' => 2, 'name' => 'card'],
        ]);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        Address::factory()->create([
            'user_id' => $user->id,
        ]);

        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        /** @var \App\Models\User $user */
        $this->actingAs($user);

        $this->post("/purchase/{$item->id}/address/update", [
            'postal_code' => '810-0001',
            'address'     => '福岡市中央区天神2-2-2',
            'building'    => '新ビル202',
        ]);

        $this->post("/purchase/{$item->id}/payment", [
            'payment_method' => 'card',
        ]);

        $response = $this->post("/purchase/{$item->id}");
        $this->followRedirects($response);

        $this->assertDatabaseHas('orders', [
            'buyer_id' => $user->id,
            'item_id'  => $item->id,
        ]);

        $this->assertDatabaseHas('order_addresses', [
            'postal_code' => '810-0001',
            'address'     => '福岡市中央区天神2-2-2',
            'building'    => '新ビル202',
        ]);
    }
}