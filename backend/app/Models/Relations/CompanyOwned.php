<?php

namespace App\Models\Relations;

use App\Models\Company;

trait CompanyOwned
{
    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Company */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function isOwnedByCompany(Company $company): bool
    {
        return $this->loadMissing('company')->company->is($company);
    }

    public function isNotOwnedByCompany(Company $company): bool
    {
        return !$this->isNotOwnedByCompany($company);
    }
}
