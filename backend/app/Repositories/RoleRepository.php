<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\Role;
use Illuminate\Support\Collection;

class RoleRepository
{
    protected $subject;

    public static function createFor(Company $company): self
    {
        return (new static)->setSubject($company);
    }

    public function setSubject($subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    protected function newQuery()
    {
        return Role::where(function ($query) {
            $query->where([
                'model_id' => $this->subject->getKey(),
                'model_type' => $this->subject->getMorphClass(),
            ]);
        });
    }

    public function hasRole($name): bool
    {
        return $this->allRoles()->contains('name', $name);
    }

    public function allRoles(): Collection
    {
        return $this->newQuery()->get();
    }

    public function getRolesFrom(array $roles = [])
    {
        if (blank($roles)) {
            return $this->newQuery()->get();
        }

        return $this->allRoles()
            ->filter(
                fn ($role) => in_array($role->name, $roles) || in_array($role->name, transform_array_to_lowercase($roles)),
            );
    }

    public function findByNameOrId($keyword): ?Role
    {
        return $this->subject->roles()
            ->where('name', $keyword)
            ->orWhere('id', $keyword)
            ->first();
    }

    public function findByName($name): ?Role
    {
        return $this->newQuery()->firstWhere('name', $name);
    }

    public function findById(int $id): ?Role
    {
        return $this->newQuery()->find($id);
    }

    public function firstOrCreate($name): Role
    {
        $role = $this->findByName($name) ?? new Role;

        if (!$role->exists) {
            $role->name = $name;
            $role->model_id = $this->subject->getKey();
            $role->model_type = $this->subject->getMorphClass();

            $role->save();
        }

        return $role;
    }
}
