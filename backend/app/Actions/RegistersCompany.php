<?php

namespace App\Actions;

use App\Events\NewCompanyRegistered;
use App\Models\Company;
use App\Models\User;

class RegistersCompany
{
    public function register(User $user, array $data)
    {
        $company = Company::create([
            'name' => $data['name'],
            'user_id' => $user->getKey(),
            'institution_type_id' => $data['institution_type'],
            'legal_form_id' => $data['legal_form'],
            'confirmed' => true,
        ]);

        event(new NewCompanyRegistered($company));

        return $company;
    }
}
