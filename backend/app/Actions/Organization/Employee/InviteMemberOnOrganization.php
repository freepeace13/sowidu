<?php

namespace App\Actions\Organization\Employee;

use App\Actions\CreateCompanyInvitation;
use App\Actions\MembershipAction;
use App\Mail\MembershipInvitationLink;
use App\Models\Company;
use App\Models\CompanyInvitation;
use App\Models\User;
use App\Rules\RoleNotFounder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use MagicLink\Actions\ResponseAction;
use MagicLink\MagicLink;

class InviteMemberOnOrganization
{
    public function invite(
        User $inviter,
        Company $company,
        array $inputs,
    ): ?CompanyInvitation {
        $validated = Validator::make($inputs, [
            'email' => ['required', 'email'],
            'note' => ['nullable', 'string'],
            'role' => ['required', 'string', new RoleNotFounder],
        ])->validate();

        $email = $validated['email'];
        $note = $validated['note'];
        $role = $validated['role'];

        if ($user = User::firstWhere('email', $email)) {
            $invitation = (new CreateCompanyInvitation)->execute($company, $user->email, $role, $note);

            return $invitation;
        }

        $membershipUrl = MagicLink::create(
            new ResponseAction(
                (new MembershipAction)->execute($validated, $company->getKey()),
            ),
        )->url;

        Mail::to($email)->queue((new MembershipInvitationLink(
            $company,
            $email,
            $membershipUrl,
            $note,
        ))->onQueue('emails'));

        return null;
    }
}
