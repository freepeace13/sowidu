<?php

namespace Modules\Chatly\Contracts;

interface CreatesConversations
{
    public function create($user, array $params, $errorBag = null);
}
