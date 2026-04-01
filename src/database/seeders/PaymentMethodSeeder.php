<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $methods = ['コンビニ払い', 'カード支払い'];

        foreach ($methods as $name) {
            \App\Models\PaymentMethod::create(['name' => $name]);
        }
    }
}
