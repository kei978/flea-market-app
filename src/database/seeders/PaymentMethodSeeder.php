<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run()
    {
        $methods = ['コンビニ払い', 'カード支払い'];

        foreach ($methods as $name) {
            PaymentMethod::create(['name' => $name]);
        }
    }
}