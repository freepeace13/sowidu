<?php

namespace Modules\Shared\Traits;

use App\Models\Company;
use App\Models\InstitutionType;
use App\Models\LegalForm;
use App\Models\Specialization;
use App\Models\User;
use App\Repositories\RoleRepository;
use App\Support\Facades\Impersonate;

trait WithOrganizationEssentials
{
    protected function retrieveOrganizationRoles()
    {
        if (!Impersonate::isImpersonating()) {
            return [];
        }

        return RoleRepository::createFor(Impersonate::tenant())
            ->allRoles()
            ->pluck('name');
    }

    public function getOrganizationRolesWithoutFounder(): array
    {
        return collect($this->retrieveOrganizationRoles())
            ->reject(fn ($role) => strtolower($role) === strtolower(Company::FOUNDER_ROLE_NAME))
            ->values()
            ->toArray();
    }

    protected function institutionTypes(): array
    {
        return InstitutionType::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->type,
                'abbrev' => $item->abbreviation,
            ];
        })->toArray();
    }

    protected function legalForms(): array
    {
        return LegalForm::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->legal_form,
                'abbrev' => $item->abbreviation,
            ];
        })->toArray();
    }

    public function allPositions(): array
    {
        return Specialization::select('title')
            ->distinct()
            ->get()
            ->pluck('title')
            ->transform(fn ($role) => ucfirst($role))
            ->toArray();
    }

    public function getCategories(User|Company $account): array
    {
        return $account->categories()
            ->get(['name'])
            ->pluck('name')
            ->toArray();
    }
}
