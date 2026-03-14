<?php

namespace App\Actions\Catalog;

use App\Actions\Traits\AsAction;
use App\Models\CatalogItemType;
use App\Models\Company;
use App\Models\User;

class CreateCatalogItemType
{
    use AsAction;

    public function handle(User $user, Company $company, string $name): CatalogItemType
    {
        return CatalogItemType::firstOrCreate([
            'user_id' => $user->getKey(),
            'company_id' => $company->getKey(),
            'name' => $name,
        ]);
    }
}
