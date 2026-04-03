<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition()
    {
        return [
            'user_id' => null,
            'postal_code' => '111-1111',
            'address'     => '福岡市中央区天神1-1-1',
            'building'    => '旧ビル101',
        ];
    }
}