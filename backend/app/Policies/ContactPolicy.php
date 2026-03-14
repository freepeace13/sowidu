<?php

namespace App\Policies;

use App\Models\Contact;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactPolicy
{
    use HandlesAuthorization;

    public function view($user, Contact $contact)
    {
        return $contact->ownerable->is($user) ?? $contact->ownerable->is($user->employer);
    }
}
