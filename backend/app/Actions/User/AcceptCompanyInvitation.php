<?php

namespace App\Actions\User;

use App\Actions\Traits\AsAction;
use App\Events\CompanyInvitationAccepted;
use App\Models\CompanyInvitation;
use App\Models\User;

class AcceptCompanyInvitation
{
    use AsAction;

    public function handle(User $user, CompanyInvitation $companyInvitation)
    {
        abort_if(!$companyInvitation->isPending(), 404);

        $companyInvitation->accept();

        event(new CompanyInvitationAccepted($companyInvitation));

        return $companyInvitation;
    }
}
