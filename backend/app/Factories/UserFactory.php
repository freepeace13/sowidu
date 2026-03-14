<?php

namespace App\Factories;

use App\Models\User;
use Illuminate\Support\Arr;
use TaylorNetwork\UsernameGenerator\Facades\UsernameGenerator;

class UserFactory
{
    /**
     * @param  mixed  $attributes
     * @return App\Models\User
     */
    public static function make(array $attributes)
    {
        $attributes['username'] = static::generateUsername($attributes);
        $attributes['confirmed'] = $attributes['confirmed'] ?? false;

        if (isset($attributes['password'])) {
            $attributes['password'] = bcrypt($attributes['password']);
        }

        $user = User::create(Arr::only($attributes, [
            'first_name',
            'last_name',
            'password',
            'email',
            'mobile',
            'username',
            'confirmed',
        ]));

        if (Arr::has($attributes, 'address')) {
            AddressFactory::make($user, $attributes['address']);
        }

        VerificationTokenFactory::make($user);

        return $user;
    }

    /**
     * @return array
     */
    private static function generateUsername(array $attributes)
    {
        if (Arr::has($attributes, 'username')) {
            return $attributes['username'];
        }

        if (Arr::has($attributes, 'email')) {
            return UsernameGenerator::usingEmail()->generate($attributes['email']);
        }

        return UsernameGenerator::generate(implode(' ', [
            $attributes['first_name'],
            $attributes['last_name'],
        ]));
    }
}
