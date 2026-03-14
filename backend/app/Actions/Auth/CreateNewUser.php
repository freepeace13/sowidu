<?php

namespace App\Actions\Auth;

use App\Actions\RegistersUser;
use App\Models\Addressbook;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class CreateNewUser
{
    public function create(array $inputs)
    {
        $validated = Validator::make($inputs, [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'min:6', 'confirmed'],
            'agreement' => ['required', 'accepted'],
            'metadata' => ['nullable', 'string'],
        ])->validate();

        $user = (new RegistersUser)
            ->register(
                Arr::except($validated, [
                    'agreement', 'password_confirmation', 'metadata',
                ]),
            );

        if ($metadata = Arr::get($validated, 'metadata')) {
            $this->parseMetaData($user, $metadata);
        }

        return $user;
    }

    protected function parseMetaData(User $user, string $metaData)
    {
        $parsed = Crypt::decrypt($metaData);

        $addressbook = Addressbook::find($parsed);

        if (!$addressbook) {
            return;
        }

        // Link addressbook to this User!
        (new LinkUserToAddressbook)->link($user, $addressbook);
    }
}
