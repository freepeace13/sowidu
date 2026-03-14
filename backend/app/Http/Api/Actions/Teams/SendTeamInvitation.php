<?php

namespace App\Http\Api\Actions\Teams;

use App\Contracts\Actions\CreatesTeamInvitations;
use App\Contracts\Actions\RunsTeamInvitationLinkActions;
use App\Contracts\Actions\SendsTeamInvitations;
use App\Mail\MembershipInvitationLink;
use App\Models\Company as Team;
use App\Models\User;
use App\Rules\RoleNotFounder;
use Illuminate\Support\Facades\Mail;
use MagicLink\Actions\ResponseAction;
use MagicLink\MagicLink;
use Packages\RestApi\RestApiAction;

class SendTeamInvitation extends RestApiAction implements SendsTeamInvitations
{
    public function getValidationRules(): array
    {
        return [
            'email' => ['required', 'email'],
            'role' => ['required', 'string', new RoleNotFounder],
            'note' => ['nullable', 'string'],
        ];
    }

    public function send(User $user, Team $team, array $inputs, $errorBag = null)
    {
        abort_unless($user->belongsToTeam($team), 403);

        $validated = $this->validate($inputs, $errorBag);

        if (User::where('email', $validated['email'])->exists()) {
            return app(CreatesTeamInvitations::class)->create($user, $team, $validated, $errorBag);
        }

        $this->sendInvitationEmail($team, $validated);
    }

    protected function sendInvitationEmail($team, $validated)
    {
        $url = MagicLink::create(new ResponseAction(
            app(RunsTeamInvitationLinkActions::class)->run($team, $validated),
        ))->url;

        Mail::to($validated['email'])->queue(
            (new MembershipInvitationLink($team, $validated['email'], $url, $validated['note']))->onQueue('emails'),
        );
    }
}
