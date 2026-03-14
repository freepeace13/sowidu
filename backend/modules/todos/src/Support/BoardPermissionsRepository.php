<?php

namespace Modules\Todos\Support;

use Illuminate\Support\Arr;
use Modules\Todos\Models\Board;

class BoardPermissionsRepository
{
    public function __construct(
        protected Board $board,
        protected string $storeKey,
    ) {}

    public function saveDefault()
    {
        collect($this->default())
            ->map(function ($permissions, $role) {
                $this->save($role, $permissions);
            })->toArray();
    }

    public function default(): array
    {
        return config('todo.board.defaults.permissions', []);
    }

    protected function save(string $role, array $permissions)
    {
        $settings = $this->board->settings;

        Arr::set($settings, $this->storeKey, [$role => $permissions]);

        $this->board->forceFill([
            'settings' => $settings,
        ])->save();
    }

    public function update(string $role, string $permission, bool $value)
    {
        throw_validation_unless($this->hasRole($role), 'Role given does not exist.');

        throw_validation_unless($this->find($role, $permission), 'Permission name given is not valid.');

        $updatedPermissions = array_merge($this->rolePermissions($role), [$permission => $value]);

        $this->save($role, $updatedPermissions);
    }

    public function all()
    {
        return collect($this->board->settings[$this->storeKey] ?? $this->default());
    }

    protected function rolePermissions(string $role): array
    {
        return $this->all()->get($role, []);
    }

    protected function hasRole(string $role): bool
    {
        return $this->all()->offsetExists($role);
    }

    public function has(string $role, string $permission): bool
    {
        return Arr::has($this->all()->toArray(), $this->role . '.' . $permission);
    }

    public function find(string $role, string $permission)
    {
        return Arr::only($this->all()->get($role), $permission);
    }

    public function allow(string $role, string $permission): bool
    {
        return head($this->find($role, $permission));
    }
}
