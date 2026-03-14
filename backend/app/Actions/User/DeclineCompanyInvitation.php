<?php

namespace App\Actions\User;

use App\Actions\Traits\AsAction;
use App\Events\CompanyInvitationDeclined;
use App\Models\CompanyInvitation;
use App\Models\User;

class DeclineCompanyInvitation
{
    use AsAction;

    public function handle(User $user, CompanyInvitation $companyInvitation)
    {
        abort_if(!$companyInvitation->isPending(), 404);

        $companyInvitation->decline();

        event(new CompanyInvitationDeclined($companyInvitation));
    }
}
