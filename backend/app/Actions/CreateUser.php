<?php

namespace App\Actions;

use App\Factories\AddressFactory;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use TaylorNetwork\UsernameGenerator\Facades\UsernameGenerator;

class CreateUser
{
    public function __invoke(array $attributes)
    {
        $user = $this->createUser($attributes);

        Artisan::call('auth:send-activation-url', [
            'user' => $user->email ?? $user->mobile,
        ]);

        if (Arr::has($attributes, 'address')) {
            AddressFactory::make($user, $attributes['address']);
        }

        return $user;
    }

    protected function createUser(array $attributes)
    {
        return User::create($this->getAttributes($attributes));
    }

    protected function getAttributes(array $attributes)
    {
        $attributes['username'] = $this->createUsername($attributes);

        $attributes['confirmed'] = isset($attributes['confirmed'])
            ? $attributes['confirmed']
            : false;

        if (isset($attributes['password'])) {
            $attributes['password'] = bcrypt($attributes['password']);
        }

        return Arr::only($attributes, [
            'first_name', 'last_name', 'password', 'email',
            'mobile', 'username', 'confirmed',
        ]);
    }

    /**
     * @return array
     */
    protected function createUsername(array $attributes)
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
