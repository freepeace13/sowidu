<?php

namespace App\Actions\Auth;

use App\Models\Addressbook;
use App\Models\User;

class LinkUserToAddressbook
{
    public function link(User $user, Addressbook $addressbook)
    {
        if ($addressbook->email !== $user->email) {
            return;
        }

        // Link this `User` to this `Addressbook`!

        // Get all addressbooks with this user email
        Addressbook::where('email', $user->email)->get()
            ->each(function (Addressbook $addressbook) use ($user) {
                $addressbook->fill([
                    'foreign_type' => null,
                ])
                    ->source()
                    ->associate($user)
                    ->save();
            });

        (new LinkUserToOrders)->link($user);
    }
}
