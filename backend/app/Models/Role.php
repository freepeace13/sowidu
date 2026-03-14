<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use Concerns\HasPermissions;

    protected $guarded = [];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permissions',
            'role_id',
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

        return $this->permissions->contains('id', $permission->id);
    }
}
