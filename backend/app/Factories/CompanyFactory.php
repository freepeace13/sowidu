<?php

namespace App\Factories;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Support\Arr;

class CompanyFactory
{
    /**
     * @return \App\Models\Company
     */
    public static function make(User $founder, array $attributes)
    {
        $attributes['user_id'] = $founder->id;
        $attributes['confirmed'] = $attributes['confirmed'] ?? false;

        $company = Company::create(
            Arr::only($attributes, [
                'user_id',
                'name',
                'institution_type_id',
                'legal_form_id',
                'confirmed',
            ]),
        );

        if (Arr::has($attributes, 'address')) {
            AddressFactory::make($company, $attributes['address']);
        }

        $employment = EmployeeFactory::make($company, $founder, [
            'specialization_id' => Specialization::first()->id,
        ]);

        $employment->syncPermissions(
            Permission::whereGuard('commercial')->get(),
        );

        return $company;
    }
}
