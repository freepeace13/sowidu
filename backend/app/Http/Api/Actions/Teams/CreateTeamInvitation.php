<?php

namespace App\Http\Api\Actions\Teams;

use App\Contracts\Actions\CreatesTeamInvitations;
use App\Events\CompanyInvitationCreated;
use App\Events\CompanyInvitationRevoked;
use App\Models\Company;
use App\Models\CompanyInvitation;
use App\Models\User;
use App\Rules\RoleNotFounder;
use Packages\RestApi\RestApiAction;

class CreateTeamInvitation extends RestApiAction implements CreatesTeamInvitations
{
    public function getValidationRules(): array
    {
        return [
            'email' => ['required', 'email'],
            'role' => ['required', 'string', new RoleNotFounder],
            'note' => ['nullable', 'string'],
        ];
    }

    public function create(User $user, Company $team, array $inputs, $errorBag = null): CompanyInvitation
    {
        $validated = $this->validate($inputs);

        $pending = CompanyInvitation::pending()
            ->whereEmail($validated['email'])
            ->first();

        if ($pending) {
            $pending->revoke();

            event(new CompanyInvitationRevoked($pending));
        }

        $invitation = CompanyInvitation::create(array_merge($validated, [
            'company_id' => $team->getKey(),
        ]));

        event(new CompanyInvitationCreated($invitation));

        return $invitation;
    }
}
