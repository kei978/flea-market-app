<?php

namespace Database\Factories;

use App\Models\OrderAddress;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderAddressFactory extends Factory
{
    protected $model = OrderAddress::class;

    public function definition()
    {
        return [
            'order_id' => Order::factory(),
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-2-3',
            'building' => 'テストビル101',
        ];
    }
}