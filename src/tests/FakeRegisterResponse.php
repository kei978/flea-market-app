<?php

namespace App\Testing;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class FakeRegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        return redirect('/mypage/profile');
    }
}
