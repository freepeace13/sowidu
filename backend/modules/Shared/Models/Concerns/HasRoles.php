<?php

namespace Modules\Shared\Models\Concerns;

use App\Models\Role;
use App\Repositories\RoleRepository;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasRoles
{
    use HasPermissions;

    public function roles(): MorphToMany
    {
        return $this->morphToMany(
            Role::class,
            'model',
            'model_roles',
            'model_id',
            'role_id',
        );
    }

    public function hasRole($roles): bool
    {
        if (is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }

        if (is_int($roles)) {
            return $this->roles->contains((new Role)->getKeyName(), $roles);
        }

        if ($roles instanceof Role) {
            return $this->roles->contains($roles->getKeyName(), $roles->getKey());
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }

            return false;
        }

        return $roles->intersect($this->roles)->isNotEmpty();
    }

    public function syncRoles(...$roles)
    {
        $this->roles()->detach();

        return $this->assignRole($roles);
    }

    public function assignRole(...$roles)
    {
        $roles = collect($roles)
            ->flatten()
            ->map(function ($role) {
                if (empty($role)) {
                    return false;
                }

                return $this->findRole($role);
            })
            ->filter(function ($role) {
                return $role instanceof Role;
            })
            ->map
            ->id
            ->all();

        $this->roles()->sync($roles, false);

        $this->load('roles');

        return $this;
    }

    public function removeRole($role)
    {
        $this->roles()->detach($this->findRole($role));

        $this->load('roles');

        return $this;
    }

    protected function findRole($role): Role
    {
        if (is_numeric($role)) {
            return RoleRepository::createFor($this->employer)->findById($role);
        }

        if (is_string($role)) {
            return RoleRepository::createFor($this->employer)->findByName($role);
        }

        return $role;
    }

    public function allPermissionNames(): array
    {
        return $this
            ->roles()
            ->with(['permissions'])
            ->get()
            ->map(fn ($role) => $role->permissions->pluck('name'))
            ->flatten(1)
            ->toArray();
    }
}
