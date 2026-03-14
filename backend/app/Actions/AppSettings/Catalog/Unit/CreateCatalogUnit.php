<?php

namespace App\Actions\AppSettings\Catalog\Unit;

use App\Actions\Traits\AsAction;
use App\Models\CatalogItemUnit;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class CreateCatalogUnit
{
    use AsAction;

    public function handle(User $user, array $inputs)
    {
        $validated = $this->validate($inputs);

        return CatalogItemUnit::make($validated)->save();
    }

    public function validate(array $inputs)
    {
        return Validator::make($inputs, [
            'name' => 'required|string|unique:catalog_item_units,name',
        ])->validate();
    }
}
