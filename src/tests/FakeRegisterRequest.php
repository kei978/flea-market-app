<?php

namespace App\Testing;

use App\Http\Requests\RegisterRequest;

class FakeRegisterRequest extends RegisterRequest
{
    public function rules()
    {
        return [
            'name' => ['required', 'max:20'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'min:8'],
        ];
    }

    public function messages()
    {
        return [
            'password.confirmed' => 'パスワードと一致しません',
        ];
    }
}