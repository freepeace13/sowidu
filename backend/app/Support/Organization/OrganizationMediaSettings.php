<?php

namespace App\Support\Organization;

use App\Jobs\Company\RemoveRevokedRolesOnMediaSettingsJob;
use App\Jobs\Media\RemoveSharedMediaToRoleJob;
use App\Models\Company;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class OrganizationMediaSettings
{
    const STORE_KEY = 'media';

    public function __construct(protected Company $company) {}

    public function saveDefaults()
    {
        $this->save($this->defaults());
    }

    protected function save(array $newMediaSettings)
    {
        $settings = $this->company->settings;

        Arr::set($settings, self::STORE_KEY, $newMediaSettings);

        return $this->company->forceFill([
            'settings' => $settings,
        ])
            ->save();
    }

    protected function defaults(): array
    {
        return config('app.default.organization.settings.media', []);
    }

    /** @return Collection */
    public function all()
    {
        return collect($this->company->settings[self::STORE_KEY] ?? $this->defaults());
    }

    public function get(string $permission): array
    {
        return Arr::flatten(Arr::first($this->all(), fn ($value, $key) => $key === $permission, []));
    }

    public function has(string $permission): bool
    {
        return Arr::has($this->all(), $permission);
    }

    public function update(string $permission, array $newValues)
    {
        throw_validation_unless(
            $this->has($permission),
            "Cannot find media settings on given value: $permission.",
        );

        $oldRoles = $this->getRolesForAutoSharing();

        $updatedSettings = $this->all()
            ->map(
                fn ($values, $permissionName) => $permission === $permissionName ? $newValues : $values,
            )
            ->toArray();

        if ($this->save($updatedSettings)) {
            $this->syncMediaSettingsToCategories($oldRoles, $newValues);
        }
    }

    protected function syncMediaSettingsToCategories($oldRoles, $newRoles)
    {
        $revokedRoles = $this->collectRevokedRoles($oldRoles, $newRoles);
        $this->syncRevokedRoles($revokedRoles);

        // Check added roles
        $newRoles = collect($newRoles)
            ->reject(fn ($role) => in_array($role, $oldRoles));

        $this->syncAddedRolesToCategories($newRoles);
    }

    protected function syncAddedRolesToCategories($newRoles)
    {
        if ($newRoles->isEmpty()) {
            return;
        }

        $this->company
            ->categories()
            ->get()
            ->each(
                fn ($category) => $category
                    ->settings()
                    ->autoShare()
                    ->add($newRoles->toArray()),
            );
    }

    protected function syncRevokedRoles($revokedRoles)
    {
        $this->removeRolesFromCategorySettings($revokedRoles);
        $this->removeSharedMediaToUnSelectedRoles($revokedRoles);
    }

    protected function collectRevokedRoles(array $oldRoles, array $newRoles): Collection
    {
        return collect($oldRoles)
            ->reject(fn ($role) => in_array($role, $newRoles));
    }

    protected function removeRolesFromCategorySettings(Collection $revokedRoles)
    {
        if ($revokedRoles->isEmpty()) {
            return;
        }

        RemoveRevokedRolesOnMediaSettingsJob::dispatch($this->company, $revokedRoles->toArray());
    }

    protected function removeSharedMediaToUnSelectedRoles(Collection $revokedRoles)
    {
        $revokedRoles
            ->each(fn ($role) => RemoveSharedMediaToRoleJob::dispatch($this->company, $role));
    }

    public function getRolesForAutoSharing(): array
    {
        $allowedRoles = collect($this->get('auto_share_to_roles'))
            ->merge([Company::FOUNDER_ROLE_NAME])
            ->unique(fn ($role) => strtolower($role))
            ->toArray();

        return collect(
            $this->company
                ->roles()
                ->get()
                ->pluck('name')
                ->toArray(),
        )->filter(fn ($role) => in_iarray($role, $allowedRoles))
            ->values()
            ->toArray();
    }
}
