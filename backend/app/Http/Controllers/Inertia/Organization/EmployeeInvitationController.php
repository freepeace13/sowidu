<?php

namespace App\Http\Controllers\Inertia\Organization;

use App\Actions\Organization\Employee\InviteMemberOnOrganization;
use App\Events\CompanyInvitationRevoked;
use App\Http\Controllers\Inertia\InertiaController;
use App\Models\CompanyInvitation;
use App\Models\Invitation;
use App\Support\Facades\Impersonate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class EmployeeInvitationController extends InertiaController
{
    public function index(Request $request)
    {
        abort_unless((bool) Impersonate::tenant(), 403);

        $invitations = Invitation::{$request->status}()->get();

        return Inertia::render('Employees/Invitations', $this->withParams([
            'invitation_status' => $request->status,

            'invitations' => $invitations->map(function ($invitation) {
                return [
                    'id' => $invitation->id,
                    'company_id' => $invitation->company_id,
                    'email' => $invitation->email,
                    'sent_at' => $invitation->created_at->diffForHumans(),
                ];
            }),
        ]));
    }

    public function store(Request $request, InviteMemberOnOrganization $inviter)
    {
        $inviter->invite($request->user(), $this->getCurrentTeam(), $request->all());

        return back(303);
    }

    public function destroy(string $token)
    {
        return DB::transaction(function () use ($token) {
            $invitation = CompanyInvitation::pending()->findOrFail($token);

            $invitation->revoke();

            event(new CompanyInvitationRevoked($invitation));

            return back(303);
        });
    }

    protected function withParams(array $params)
    {
        return array_merge([
            'title' => 'Employees',
        ], $params);
    }
}
