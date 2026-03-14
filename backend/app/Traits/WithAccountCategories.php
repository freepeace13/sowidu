<?php

namespace App\Traits;

use App\Models\Company;
use App\Models\User;
use App\Support\Facades\Impersonate;

trait WithAccountCategories
{
    public function getAccountCategories(User|Company|null $account = null): array
    {
        $account = $account ?? Impersonate::account();

        return $account->categories()
            ->get(['name'])
            ->pluck('name')
            ->toArray();
    }
}
