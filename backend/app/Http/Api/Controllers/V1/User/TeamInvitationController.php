<?php

namespace App\Http\Api\Controllers\V1\User;

use App\Actions\User\AcceptCompanyInvitation;
use App\Actions\User\DeclineCompanyInvitation;
use App\Http\Api\Resources\V1\CompanyInvitationResource;
use App\Models\CompanyInvitation;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Packages\RestApi\RestfulController;

class TeamInvitationController extends RestfulController
{
    public function index(Request $request)
    {
        return CompanyInvitationResource::collection(
            UserRepository::make($request->user())->getPendingCompanyInvitations(),
        );
    }

    public function accept(Request $request, CompanyInvitation $companyInvitation)
    {
        $invitation = AcceptCompanyInvitation::run(
            $request->user(),
            $companyInvitation,
        );

        return new CompanyInvitationResource($invitation);
    }

    public function decline(Request $request, CompanyInvitation $companyInvitation)
    {
        $invitation = DeclineCompanyInvitation::run(
            $request->user(),
            $companyInvitation,
        );

        return new CompanyInvitationResource($invitation);
    }
}
