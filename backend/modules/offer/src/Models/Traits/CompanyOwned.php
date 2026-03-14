<?php

namespace Modules\Offer\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait for models that are owned by a company.
 * Uses config-driven class resolution for the Company model.
 */
trait CompanyOwned
{
    public function company(): BelongsTo
    {
        return $this->belongsTo($this->getCompanyModelClass());
    }

    public function isOwnedByCompany(Model $company): bool
    {
        return $this->loadMissing('company')->company->is($company);
    }

    public function isNotOwnedByCompany(Model $company): bool
    {
        return !$this->isOwnedByCompany($company);
    }

    protected function getCompanyModelClass(): string
    {
        return config('offer.models.company', \App\Models\Company::class);
    }
}
