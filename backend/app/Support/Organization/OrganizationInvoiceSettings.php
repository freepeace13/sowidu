<?php

namespace App\Support\Organization;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class OrganizationInvoiceSettings
{
    const STORE_KEY = 'invoice';

    public function __construct(protected Company $company) {}

    /** @return Collection */
    public function all()
    {
        return collect($this->company->settings[self::STORE_KEY] ?? $this->defaults());
    }

    protected function defaults(): array
    {
        return [
            'managing_director' => [],
        ];
    }

    protected function save(array $newSettings)
    {
        $settings = $this->company->settings;

        Arr::set($settings, self::STORE_KEY, $newSettings);

        return $this->company->forceFill([
            'settings' => $settings,
        ])
            ->save();
    }

    public function updateManagingDirector(User $user)
    {
        $updatedSettings = $this->all()
            ->map(
                fn ($values, $permissionName) => $permission === $permissionName ? $newValues : $values,
            )
            ->toArray();

        $this->save($updatedSettings);
    }
}
