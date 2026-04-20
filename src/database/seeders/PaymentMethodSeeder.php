<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run()
    {
        PaymentMethod::updateOrCreate(
            ['id' => 1],
            ['name' => 'convenience']
        );

        PaymentMethod::updateOrCreate(
            ['id' => 2],
            ['name' => 'card']
        );
    }
}
