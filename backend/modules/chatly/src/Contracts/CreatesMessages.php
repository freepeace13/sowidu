<?php

namespace Modules\Chatly\Contracts;

use Musonza\Chat\Models\Conversation;

interface CreatesMessages
{
    public function create($user, Conversation $conversation, array $params, $errorBag = null);
}
