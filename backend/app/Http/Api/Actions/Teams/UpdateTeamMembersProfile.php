<?php

namespace App\Http\Api\Actions\Teams;

use App\Actions\Organization\Employee\UpdateEmployeeRates;
use App\Actions\Organization\Employee\UpdateEmployeeRoles;
use App\Contracts\Actions\UpdatesTeamMembersProfile;
use App\Events\Organization\MembersRateUpdated;
use App\Models\Company as Team;
use App\Models\Employee as TeamMember;
use App\Models\User;
use App\Repositories\RoleRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Packages\RestApi\RestApiAction;

class UpdateTeamMembersProfile extends RestApiAction implements UpdatesTeamMembersProfile
{
    protected $team;

    protected $actor;

    protected $member;

    public function getValidationRules(): array
    {
        $validRoles = RoleRepository::createFor($this->team)
            ->allRoles()
            ->pluck('name')
            ->toArray();

        return [
            'contactNumber' => ['nullable', 'string'],
            'roles' => ['nullable', 'array', Rule::in($validRoles)],
            'rates' => ['nullable', 'array'],
            'rates.rate' => ['string'],
            'rates.currency' => ['string'],
        ];
    }

    public function update(User $actor, Team $team, TeamMember $member, array $inputs, $errorBag = null): TeamMember
    {
        $this->team = $team;
        $this->actor = $actor;
        $this->member = $member;

        $this->ensureUpdatingTeamMember();

        $validated = $this->validate($inputs, $errorBag);

        $this->updateMembersBasicInfo($validated);
        $this->updateMembersRoles($validated);
        $this->updateMembersRate($validated);

        return $member->fresh();
    }

    protected function ensureUpdatingTeamMember()
    {
        $isBelongsToTeam = $this->member->user->belongsToTeam($this->team);

        abort_unless($isBelongsToTeam, Response::HTTP_FORBIDDEN);
    }

    protected function updateMembersRate(array $inputs)
    {
        if ($rates = Arr::get($inputs, 'rates')) {
            UpdateEmployeeRates::run($this->actor, $this->team, $this->member, [
                'rate' => $rates['rate'],
                'currency' => $rates['currency'],
            ]);

            event(new MembersRateUpdated($this->team, $this->member, $this->member->rate->fresh()));
        }
    }

    protected function updateMembersRoles(array $inputs)
    {
        if ($roles = Arr::get($inputs, 'roles')) {
            UpdateEmployeeRoles::run($this->actor, $this->team, $this->member, [
                'roles' => $roles,
            ]);
        }
    }

    protected function updateMembersBasicInfo(array $inputs)
    {
        if ($contactNumber = Arr::get($inputs, 'contactNumber')) {
            // update member's contact number
        }
    }
}
