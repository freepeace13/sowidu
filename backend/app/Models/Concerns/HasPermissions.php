<?php

namespace App\Models\Concerns;

use App\Exceptions\PermissionDoesNotExist;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasPermissions
{
    public function permissions(): MorphToMany
    {
        return $this->morphToMany(
            Permission::class,
            'model',
            'model_permissions',
            'model_id',
            'permission_id',
        );
    }

    public function hasPermissionTo($permission): bool
    {
        if (is_string($permission)) {
            $permission = Permission::firstWhere('name', $permission);
        }

        if (is_int($permission)) {
            $permission = Permission::find($permission);
        }

        if (!$permission instanceof Permission) {
            throw new PermissionDoesNotExist;
        }

        return $this->hasDirectPermission($permission) || $this->hasPermissionViaRole($permission);
    }

    public function hasDirectPermission($permission): bool
    {
        $permission = $this->findPermission($permission);

        return $this->permissions->contains('id', $permission->id);
    }

    protected function hasPermissionViaRole(Permission $permission): bool
    {
        return $this->hasRole($permission->roles);
    }

    public function checkPermissionTo($permission): bool
    {
        try {
            return $this->hasPermissionTo($permission);
        } catch (PermissionDoesNotExist $e) {
            return false;
        }
    }

    public function givePermissionTo(...$permissions)
    {
        $permissions = collect($permissions)
            ->flatten()
            ->map(function ($permission) {
                if (empty($permission)) {
                    return false;
                }

                return $this->findPermission($permission);
            })
            ->filter(function ($permission) {
                return $permission instanceof Permission;
            })
            ->map->id
            ->all();

        $this->permissions()->sync($permissions, false);

        $this->load('permissions');

        return $this;
    }

    public function syncPermissions(...$permissions)
    {
        $this->permissions()->detach();

        return $this->givePermissionTo($permissions);
    }

    public function revokePermissionTo($permission)
    {
        $this->permissions()->detach($this->findPermission($permission));

        $this->load('permissions');

        return $this;
    }

    protected function findPermission($permission): Permission
    {
        if (is_string($permission)) {
            return Permission::firstWhere('name', $permission);
        }

        if (is_int($permission)) {
            return Permission::find($permission);
        }

        return $permission;
    }
}
