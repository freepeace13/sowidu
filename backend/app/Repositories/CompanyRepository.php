<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Contracts\Auth\Access\Authorizable;

class CompanyRepository
{
    /**
     * Find the employee instance of the company owner.
     *
     * @param  App\Models\Company  $company
     * @return App\Models\Employee
     */
    public function findOwnerEmployee(Company $company)
    {
        return $company->employees()->where([
            'user_id' => $company->user->id,
        ])->first();
    }

    /**
     * Determine if the given employee is the owner.
     *
     * @param  App\Models\Employee  $employee
     * @return bool
     */
    public function checkIfOwner(Authorizable $user)
    {
        if ($user instanceof User) {
            return false;
        }

        return $this->checkIfOwnerOf($user, $user->employer);
    }

    /**
     * Determine if the employee is the owner of the given company.
     *
     * @param  Illuminate\Auth\Access\Authorizable  $user
     * @param  App\Models\Company  $company
     * @return bool
     */
    public function checkIfOwnerOf(Authorizable $user, Company $company)
    {
        if ($user instanceof User) {
            return false;
        }

        return $this->findOwnerEmployee($company)->is($user);
    }

    /**
     * Determine if the given user is employed to the company
     *
     * @param  App\Models\Company  $company
     * @param  App\Models\User
     * @return bool
     */
    public function hasEmployeeUser(Company $company, User $user)
    {
        return $company->employees->contains('user_id', $user->id);
    }

    /**
     * Check if company is authenticated by the specified user (employee)
     *
     * @param  App\Models\User  $user
     */
    public function checkHasAuthUser(Company $company, User $user): bool
    {
        return $company->loggedBookUsers()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Helper function that authenticate user (employee) in the company account
     *
     * @param  App\Models\User  $user
     * @return void
     */
    public function authAsEmployee(Company $company, User $user)
    {
        $company->loggedBookUsers()->attach($user->id);

        $this->findEmployeeUser($company, $user)
            ->setOnline()
            ->save();

        return $company->createToken($company->name);
    }

    /**
     * Helper function that logout user (employee) in the company account
     *
     * @param  App\Models\User  $user
     * @return void
     */
    public function logoutAsEmployee(Company $company, User $user)
    {
        $company->loggedBookUsers()->detach($user->id);

        if (!$this->hasAuthUsers($company)) {
            $company->setOffline()->save();
        }

        $this->findEmployeeUser($company, $user)
            ->setOffline()
            ->save();
    }

    /**
     * Check if company has actively authenticated by multiple employees
     *
     * @return bool
     */
    public function hasAuthUsers(Company $company)
    {
        return $company->loggedBookUsers()->count() > 0;
    }

    /**
     * Helper function that get the employee instance based on the
     * user instance passed
     *
     * @param  App\Models\User  $user
     * @return App\Models\Employee
     */
    public function findEmployeeUser(Company $company, User $user)
    {
        return $company->employees()
            ->confirmed()
            ->where('user_id', $user->id)
            ->first();
    }

    /**
     * Determine the given user has already requested for employment
     *
     * @param  App\Models\Company  $company
     * @param  App\Models\User  $user
     * @return bool
     */
    public function hasRequestedEmployment(Company $company, User $user)
    {
        return !is_null($this->findRequest($company, $user));
    }

    /**
     * Find the given user's employment request
     *
     * @param  App\Models\Company  $company
     * @param  App\Models\User  $user
     * @return App\Models\EmploymentReqyest|null
     */
    public function findRequest(Company $company, User $user)
    {
        return $company->employmentRequests()
            ->whereCandidate($user)
            ->first();
    }
}
