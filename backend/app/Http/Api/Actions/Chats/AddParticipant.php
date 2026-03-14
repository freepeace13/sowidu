<?php

namespace App\Http\Api\Actions\Chats;

use App\Contracts\Actions\AddsConversationParticipants;
use Illuminate\Support\Facades\Gate;
use Musonza\Chat\Models\Conversation;
use Musonza\Chat\Traits\Messageable;
use Packages\RestApi\RestApiAction;
use Packages\Urn\UrnManager;

class AddParticipant extends RestApiAction implements AddsConversationParticipants
{
    public function add($user, $joiner, Conversation $conversation, $errorBag = null)
    {
        Gate::forUser($user)->authorize('addParticipants', $conversation);

        if (is_string($joiner)) {
            $joiner = UrnManager::resolve($joiner);
        }

        if (!in_array(Messageable::class, class_uses_recursive($joiner))) {
            $this->throwValidationError(['urn' => 'The urn is invalid.']);
        }

        if ($participation = $conversation->participantFromSender($joiner)) {
            return $participation;
        }

        return $conversation
            ->addParticipants([$joiner])
            ->participantFromSender($joiner);
    }
}
