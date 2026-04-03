<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    public function run()
    {
        Item::create([
            'user_id' => 1,
            'title' => '腕時計',
            'brand' => 'Rolax',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'price' => 15000,
            'category_ids' => [],
            'condition' => 1,
            'status' => 1,
            'image_path' => 'items/Armani+Mens+Clock.jpg',
        ]);

        Item::create([
            'user_id' => 2,
            'title' => 'HDD',
            'brand' => '西芝',
            'description' => '高速で信頼性の高いハードディスク',
            'price' => 5000,
            'category_ids' => [],
            'condition' => 2,
            'status' => 1,
            'image_path' => 'items/HDD+Hard+Disk.jpg',
        ]);

        Item::create([
            'user_id' => 3,
            'title' => '玉ねぎ3束',
            'brand' => 'なし',
            'description' => '新鮮な玉ねぎ3束のセット',
            'price' => 300,
            'category_ids' => [],
            'condition' => 3,
            'status' => 1,
            'image_path' => 'items/iLoveIMG+d.jpg',
        ]);

        Item::create([
            'user_id' => 4,
            'title' => '革靴',
            'brand' => '',
            'description' => 'クラシックなデザインの革靴',
            'price' => 4000,
            'category_ids' => [],
            'condition' => 4,
            'status' => 1,
            'image_path' => 'items/Leather+Shoes+Product+Photo.jpg',
        ]);

        Item::create([
            'user_id' => 5,
            'title' => 'ノートPC',
            'brand' => '',
            'description' => '高性能なノートパソコン',
            'price' => 45000,
            'category_ids' => [],
            'condition' => 1,
            'status' => 1,
            'image_path' => 'items/Living+Room+Laptop.jpg',
        ]);

        Item::create([
            'user_id' => 6,
            'title' => 'マイク',
            'brand' => 'なし',
            'description' => '高音質のレコーディング用マイク',
            'price' => 8000,
            'category_ids' => [],
            'condition' => 2,
            'status' => 1,
            'image_path' => 'items/Music+Mic+4632231.jpg',
        ]);

        Item::create([
            'user_id' => 7,
            'title' => 'ショルダーバッグ',
            'brand' => '',
            'description' => 'おしゃれなショルダーバッグ',
            'price' => 3500,
            'category_ids' => [],
            'condition' => 3,
            'status' => 1,
            'image_path' => 'items/Purse+fashion+pocket.jpg',
        ]);

        Item::create([
            'user_id' => 8,
            'title' => 'タンブラー',
            'brand' => 'なし',
            'description' => '使いやすいタンブラー',
            'price' => 500,
            'category_ids' => [],
            'condition' => 4,
            'status' => 1,
            'image_path' => 'items/Tumbler+souvenir.jpg',
        ]);

        Item::create([
            'user_id' => 9,
            'title' => 'コーヒーミル',
            'brand' => 'Starbacks',
            'description' => '手動のコーヒーミル',
            'price' => 4000,
            'category_ids' => [],
            'condition' => 1,
            'status' => 1,
            'image_path' => 'items/Waitress+with+Coffee+Grinder.jpg',
        ]);

        Item::create([
            'user_id' => 10,
            'title' => 'メイクセット',
            'brand' => '',
            'description' => '便利なメイクアップセット',
            'price' => 2500,
            'category_ids' => [],
            'condition' => 2,
            'status' => 1,
            'image_path' => 'items/外出メイクアップセット.jpg',
        ]);
    }
}