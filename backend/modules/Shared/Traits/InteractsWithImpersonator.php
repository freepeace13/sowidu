<?php

namespace Modules\Shared\Traits;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait InteractsWithImpersonator
{
    /**
     * Get authenticated user/employee
     */
    protected function user()
    {
        return $this->getCurrentEmployee() ?? $this->getCurrentUser();
    }

    protected function account(): Company|User
    {
        return $this->getCurrentTeam() ?? $this->getCurrentUser();
    }

    public function getCurrentEmployee()
    {
        return $this->getCurrentUser()
            ->teamMembership($this->getCurrentTeam());
    }

    /**
     * @return User|\Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected function getCurrentUser()
    {
        return Auth::user();
    }

    protected function getCurrentTeamId(): ?int
    {
        return $this->getCurrentTeam()
            ?->id;
    }

    protected function getCurrentTeam(): ?Company
    {
        return $this->getCurrentUser()
            ?->currentTeam();
    }

    /** @return Company|null */
    protected function getCurrentCompany()
    {
        return $this->getCurrentTeam();
    }

    /**
     * Identify authenticated user if impersonating or not
     *
     * @param  string|\Illuminate\Support\Facades\Auth  $value
     * @return bool
     */
    protected function isImpersonating($value = null)
    {
        $currentTeam = $this->getCurrentTeam();

        if ($value && $currentTeam) {
            return $currentTeam->is($value);
        }

        return $currentTeam !== null;
    }

    protected function isNotImpersonating($value = null)
    {
        return $this->isImpersonating($value) === false;
    }

    protected function authAsEmployee($value = null)
    {
        return $this->isImpersonating($value);
    }

    protected function authIsPrivateUser($value = null)
    {
        return $this->isNotImpersonating($value);
    }
}
