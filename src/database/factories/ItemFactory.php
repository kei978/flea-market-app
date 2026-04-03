<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'user_id' => null, // ★ テスト側で指定するため null にする
            'title' => $this->faker->word(),
            'brand' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(100, 5000),
            'condition' => 1,
            'status' => 1,
            'image_path' => 'test.jpg',
            'category_ids' => json_encode([1, 2]),
        ];
    }
}