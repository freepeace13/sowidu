<?php

namespace App\Actions\Addressbook\Person;

use App\Models\Addressbook;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class RemovesPersonAddressbook
{
    public function remove(User $user, Addressbook $addressbook, ?int $teamId = null)
    {
        Gate::forUser($user)->authorize('remove', $addressbook);

        $addressbook->delete();
    }
}
