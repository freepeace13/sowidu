<?php

namespace App\Support\Category;

use App\Jobs\Media\AutoShareMediaWithCategoryToRoleJob;
use App\Jobs\Media\RemoveSharedMediaOnCategoryRoleJob;
use App\Models\Category;
use App\Models\Company;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class AutoShareToRoles
{
    const KEY = 'auto_share_to_roles';

    public function __construct(protected Category $category) {}

    public function defaults(): array
    {
        return [Company::FOUNDER_ROLE_NAME];
    }

    public function all(): Collection
    {
        return collect($this->category->settings[self::KEY] ?? $this->defaults());
    }

    public function values()
    {
        return [self::KEY => $this->all()->toArray()];
    }

    public function has(string $category): bool
    {
        return $this->all()->has($category);
    }

    protected function getMediaSettingsAutoSharedRoles(): array
    {
        /** @var Company */
        $company = $this->category
            ->loadMissing(['ownerable'])
            ->ownerable;

        return $company
            ->settings()
            ->media()
            ->getRolesForAutoSharing();
    }

    protected function validate(array $newRoles)
    {
        // Validate given roles is valid!
        $organizationRoles = $this->category
            ->loadMissing(['ownerable'])
            ->ownerable
            ->roles()
            ->get(['name'])
            ->pluck('name')
            ->toArray();

        $invalidRoles = collect($newRoles)
            ->map(function ($role) use ($organizationRoles) {
                if (blank($role)) {
                    return;
                }

                if (in_array($role, $organizationRoles)) {
                    return collect($organizationRoles)
                        ->first(fn ($organizationRole) => $organizationRole == $role ? $organizationRole : null);
                }

                if (in_iarray($role, $organizationRoles)) {
                    return collect($organizationRoles)
                        ->first(fn ($organizationRole) => strtolower($organizationRole) == strtolower($role) ? $organizationRole : null);
                }
            })
            ->filter()
            ->reject(fn ($newRole) => in_array($newRole, $organizationRoles))
            ->filter();

        throw_validation_if(
            $invalidRoles->isNotEmpty(),
            'Role is invalid: ' . $invalidRoles->join(', ', '.'),
        );
    }

    public function add(array $newRoles)
    {
        // Get old roles
        $oldRoles = $this->all()->toArray();

        // Merge old roles to new roles
        $updatedRoles = array_merge($oldRoles, $newRoles);

        $this->update($updatedRoles);
    }

    public function update(array $newRoles)
    {
        $this->validate($newRoles);

        $oldRoles = $this->all()->toArray();

        $roles = $this->mergeRolesFromMediaSettings($newRoles);

        if ($this->save($roles)) {
            $this->syncAutoSharing($oldRoles, $newRoles);
        }
    }

    public function remove(array $revokedRoles)
    {
        // Generate new roles without revoked roles
        $newRoles = collect($this->all()->toArray())
            ->reject(fn ($role) => in_iarray($role, $revokedRoles))
            ->toArray();

        $this->update($newRoles);
    }

    protected function syncAutoSharing(array $oldRoles, array $newRoles)
    {
        $revokedRoles = collect($oldRoles)
            ->reject(fn ($role) => in_array($role, $newRoles))
            ->values();

        if ($revokedRoles->isNotEmpty()) {
            // Revoked roles
            $this->removeSharedMediaToUnSelectedRoles($oldRoles, $newRoles);
        }

        $addedRoles = collect($newRoles)
            ->reject(fn ($role) => in_array($role, $oldRoles))
            ->values();

        if ($addedRoles->isNotEmpty()) {
            $this->addSharedMediaToNewRoles($addedRoles);
        }
    }

    protected function addSharedMediaToNewRoles(Collection $newRoles)
    {
        $newRoles->each(
            fn ($role) => AutoShareMediaWithCategoryToRoleJob::dispatch($this->category, $role),
        );
    }

    public function mergeRolesFromMediaSettings(array $newRoles): array
    {
        return collect([...$this->getMediaSettingsAutoSharedRoles(), ...$newRoles])
            ->unique()
            ->values()
            ->toArray();
    }

    protected function removeSharedMediaToUnSelectedRoles(array $oldRoles, array $newRoles)
    {
        collect($oldRoles)
            ->reject(fn ($role) => in_array($role, $newRoles))
            ->each(
                fn ($role) => RemoveSharedMediaOnCategoryRoleJob::dispatch(
                    $this->category->ownerable,
                    $this->category,
                    $role,
                ),
            );
    }

    protected function save(array $newRoles)
    {
        $settings = $this->category->settings;

        Arr::set($settings, self::KEY, $newRoles);

        return $this->category->forceFill([
            'settings' => $settings,
        ])->save();
    }
}
