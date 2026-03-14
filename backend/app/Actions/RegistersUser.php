<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Auth\Events\Registered;

class RegistersUser
{
    public function register(array $data)
    {
        $user = User::create([
            'username' => $data['username'] ?? null,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        event(new Registered($user));

        return $user;
    }
}
