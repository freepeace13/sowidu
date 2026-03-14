<?php

namespace App\Modules\Addressbook;

use App\Models\Addressbook;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AddressbookHelper
{
    public function __construct() {}

    public static function make()
    {
        return new static;
    }

    public function findUser(string $email): ?User
    {
        return Cache::remember(
            "addressbooks.$email.user",
            now()->addMinutes(10),
            fn () => User::where('email', $email)->first(),
        );
    }

    public function getAddressbookIdsFromUser(User $user)
    {
        return Cache::remember(
            "addressbooks.ids.user.{$user->id}",
            now()->addMinutes(10),
            fn () => Addressbook::where('email', $user->email)
                ->orWhereMorphedTo('source', $user)
                ->pluck('id'),
        );
    }
}
