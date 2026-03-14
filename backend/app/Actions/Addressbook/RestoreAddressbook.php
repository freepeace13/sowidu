<?php

namespace App\Actions\Addressbook;

use App\Actions\Traits\AsAction;
use App\Models\Addressbook;
use App\Models\Company;
use App\Models\User;

class RestoreAddressbook
{
    use AsAction;

    public function handle(User $user, Company $company, Addressbook $addressbook)
    {
        $addressbook->restore();

        return $addressbook;
    }
}
