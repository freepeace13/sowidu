<?php

namespace App\Http\Controllers\Inertia\Organization;

use App\Actions\User\AcceptCompanyInvitation;
use App\Actions\User\DeclineCompanyInvitation;
use App\Http\Controllers\Inertia\InertiaController;
use App\Models\CompanyInvitation;
use Illuminate\Support\Facades\DB;

class InviteeResponseController extends InertiaController
{
    public function accept(string $token)
    {
        return DB::transaction(function () use ($token) {
            $invitation = CompanyInvitation::pending()
                ->findOrFail($token);

            AcceptCompanyInvitation::run($this->getCurrentUser(), $invitation);

            return back(303);
        });
    }

    public function decline(string $token)
    {
        return DB::transaction(function () use ($token) {
            $invitation = CompanyInvitation::pending()
                ->findOrFail($token);

            DeclineCompanyInvitation::run($this->getCurrentUser(), $invitation);

            return back(303);
        });
    }
}
