<?php

namespace App\Actions\Organization\Settings\Tax;

use App\Actions\Traits\AsAction;
use App\Models\Company;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class DeleteTax
{
    use AsAction;

    public function handle(User $user, Company $company, Tax $tax)
    {
        Gate::forUser($user)->authorize('deleteTax', [$company, $tax]);

        abort_if(
            $tax->isNotOwnedByCompany($company),
            403,
        );

        $tax->delete();
    }
}
