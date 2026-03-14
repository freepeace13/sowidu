<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginUser
{
    public function login(User $user)
    {
        Auth::guard('web')->login($user);

        $token = $user->createToken($user->email);

        redirect()->route('auth.authorize', [
            'accessToken' => $token->accessToken,
        ]);
    }
}
