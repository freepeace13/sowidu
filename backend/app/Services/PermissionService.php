<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use App\Traits\InteractsWithImpersonator;

class PermissionService
{
    use InteractsWithImpersonator;

    protected User|Employee $account;

    public function __construct($account = null)
    {
        $this->account = $account ?? $this->user();
    }

    public function forAccount(User|Employee $user): static
    {
        return new static($user);
    }

    public static function allows(string $permission): bool
    {
        return (new static)->allowsTo($permission);
    }

    public function allowsTo(string $permission): bool
    {
        return $this->account->hasPermissionTo($permission);
    }
}
