<?php

namespace App\Actions\Chat;

use App\Models\Company as Team;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Musonza\Chat\Facades\ChatFacade as Chat;

class CreatesConversation
{
    public function create($user, array $params)
    {
        Validator::make($params, [
            'userId' => ['required', 'exists:users,id'],
            'teamId' => ['nullable', 'exists:companies,id'],
        ])->validateWithBag('createConversation');

        $recipient = $this->determineRecipient($user, $params);

        if ($conversation = Chat::conversations()->between($user, $recipient)) {
            return $conversation;
        }

        return Chat::makeDirect()->createConversation([$user, $recipient]);
    }

    protected function determineRecipient($user, $params)
    {
        $recipient = User::find($params['userId']);

        $teamId = $params['teamId'] ?? null;

        if ($teamId && ($team = Team::find($teamId))) {
            $recipient = $team->memberships()->firstWhere('user_id', $user->getKey());
        }

        return $recipient;
    }
}
