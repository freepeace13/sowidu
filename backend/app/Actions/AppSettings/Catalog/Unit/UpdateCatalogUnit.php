<?php

namespace App\Actions\AppSettings\Catalog\Unit;

use App\Actions\Traits\AsAction;
use App\Models\CatalogItemUnit;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateCatalogUnit
{
    use AsAction;

    public function handle(User $user, CatalogItemUnit $catalogItemUnit, array $inputs)
    {
        $validated = $this->validate($catalogItemUnit, $inputs);

        return $catalogItemUnit->update($validated);
    }

    protected function validate(CatalogItemUnit $catalogItemUnit, array $inputs)
    {
        return Validator::make($inputs, [
            'name' => [
                'required',
                'string',
                Rule::unique('catalog_item_units', 'name')->ignore($catalogItemUnit),
            ],
        ])->validate();
    }
}
