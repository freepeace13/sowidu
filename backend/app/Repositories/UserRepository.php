<?php

namespace App\Repositories;

use App\Models\CompanyInvitation;
use App\Models\User;

class UserRepository
{
    public function __construct(protected User $user) {}

    public static function make(User $user): static
    {
        return new static($user);
    }

    public function getPendingCompanyInvitations()
    {
        return CompanyInvitation::where('email', $this->user->email)->pending()
            ->get();
    }

    public function getCompanies()
    {
        return $this->user->companies()
            ->get();
    }
}
