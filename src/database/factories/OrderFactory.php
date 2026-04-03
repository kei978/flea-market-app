<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Item;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'item_id' => Item::factory(),
            'buyer_id' => User::factory(),
            'payment_method_id' => PaymentMethod::factory(),
            'price' => $this->faker->numberBetween(100, 5000),
            'status' => 1,
        ];
    }
}