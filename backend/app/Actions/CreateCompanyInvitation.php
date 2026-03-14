<?php

namespace App\Actions;

use App\Events\CompanyInvitationCreated;
use App\Events\CompanyInvitationRevoked;
use App\Models\Company;
use App\Models\CompanyInvitation as Invitation;

class CreateCompanyInvitation
{
    public function execute(Company $company, string $email, string $role, $note = null)
    {
        $pending = Invitation::pending()->whereEmail($email)->first();

        if ($pending) {
            $pending->revoke();

            event(new CompanyInvitationRevoked($pending));
        }

        $invite = Invitation::create([
            'company_id' => $company->getKey(),
            'email' => $email,
            'note' => $note,
            'role' => $role,
        ]);

        event(new CompanyInvitationCreated($invite));

        return $invite;
    }
}
