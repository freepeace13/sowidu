<?php

namespace Modules\Chatly\Actions;

use Modules\Chatly\Contracts\AddsConversationParticipants;
use Modules\Chatly\Contracts\External\AuthorizationContract;
use Modules\Chatly\Contracts\External\UrnResolverContract;
use Musonza\Chat\Models\Conversation;
use Musonza\Chat\Traits\Messageable;
use Packages\RestApi\RestApiAction;

class AddParticipant extends RestApiAction implements AddsConversationParticipants
{
    public function __construct(
        protected AuthorizationContract $authorization,
        protected UrnResolverContract $urnResolver,
    ) {}

    public function add($user, $joiner, Conversation $conversation, $errorBag = null)
    {
        $this->authorization->authorize($user, 'addParticipants', $conversation);

        if (is_string($joiner)) {
            $joiner = $this->urnResolver->resolve($joiner);
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
