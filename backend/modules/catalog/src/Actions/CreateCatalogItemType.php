<?php

namespace Modules\Catalog\Actions;

use App\Actions\Traits\AsAction;
use App\Models\Company;
use App\Models\User;
use Modules\Catalog\Contracts\CreatesCatalogItemType as CreatesCatalogItemTypeContract;
use Modules\Catalog\Models\CatalogItemType;

class CreateCatalogItemType implements CreatesCatalogItemTypeContract
{
    use AsAction;

    public function handle(User $user, Company $company, string $name): CatalogItemType
    {
        return $this->create($user, ['name' => $name], $company->getKey());
    }

    public function create(User $user, array $inputs, $teamId = null, $errorBag = null): CatalogItemType
    {
        return CatalogItemType::firstOrCreate([
            'user_id' => $user->getKey(),
            'company_id' => $teamId,
            'name' => $inputs['name'],
        ]);
    }
}
