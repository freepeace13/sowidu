<?php

namespace App\Contracts\Actions;

interface CreatesConversations
{
    public function create($user, array $params, $errorBag = null);
}
