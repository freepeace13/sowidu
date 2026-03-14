<?php

namespace App\Contracts\Auth;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Role
{
    /**
     * A role may be given various permissions.
     */
    public function permissions(): BelongsToMany;

    /**
     * Find a role by its name and guard name.
     *
     * @param  string|null  $guardName
     * @return \Spatie\Permission\Contracts\Role
     *
     * @throws \Spatie\Permission\Exceptions\RoleDoesNotExist
     */
    public static function findByName(string $name, string $guardName, $group): self;

    /**
     * Find a role by its id and guard name.
     *
     * @param  string|null  $guardName
     * @return \Spatie\Permission\Contracts\Role
     *
     * @throws \Spatie\Permission\Exceptions\RoleDoesNotExist
     */
    public static function findById(int $id, string $guardName, $group): self;

    /**
     * Find or create a role by its name and guard name.
     *
     * @param  string|null  $guardName
     * @return \Spatie\Permission\Contracts\Role
     */
    public static function findOrCreate(string $name, string $guardName, $group): self;

    /**
     * Determine if the user may perform the given permission.
     *
     * @param  string|\Spatie\Permission\Contracts\Permission  $permission
     */
    public function hasPermissionTo($permission): bool;
}
