<?php

namespace Packages\Tenancy\Testing;

use App\Models\Company;
use App\Models\User;

/**
 * Trait for test classes to easily set up team context.
 *
 * Provides helper methods for authenticating users with team context in tests.
 */
trait WithTeamContext
{
    /**
     * Authenticate a user and set their current team.
     * This is a convenience method that combines actingAs() and switchTeam().
     *
     * @return $this
     */
    protected function actingAsWithTeam(User $user, ?Company $team = null): self
    {
        if ($team && !$user->belongsToTeam($team)) {
            $user->teams()->attach($team);
        }

        if ($team) {
            $user->switchTeam($team);
        }

        return $this->actingAs($user);
    }

    /**
     * Set up a user with a company and authenticate them.
     * Creates a company owned by the user and sets it as the current team.
     *
     * @return array{user: \App\Models\User, company: \App\Models\Company}
     */
    protected function setUpUserWithCompany(?User $user = null): array
    {
        $user = $user ?? \App\Models\User::factory()->create();
        $company = \App\Models\Company::factory()->forUser($user)->create();
        $user->teams()->attach($company);
        $user->switchTeam($company);

        return [
            'user' => $user,
            'company' => $company,
        ];
    }
}
