<?php

namespace App\Http\Api\Actions\Auth;

use App\Contracts\Actions\SwitchesAccount;
use App\Models\Company as Team;
use App\Models\User;
use Packages\RestApi\RestApiAction;
use Packages\Urn\UrnManager;

class SwitchTeam extends RestApiAction implements SwitchesAccount
{
    public function switch(User $user, $urn, $errorBag = null): ?Team
    {
        $account = UrnManager::resolve($urn);

        $switched = match (true) {
            $account instanceof User => $this->switchBackToPersonalAccount($user),
            $account instanceof Team => $user->switchTeam($account),
            default => false,
        };

        if (!$switched) {
            $this->throwValidationError([
                'urn' => 'The urn is invalid.',
            ], $errorBag);
        }

        $user->refresh();

        return $user->currentTeam();
    }

    private function switchBackToPersonalAccount($user)
    {
        return $user->forceFill(['current_team_id' => null])->save();
    }
}
