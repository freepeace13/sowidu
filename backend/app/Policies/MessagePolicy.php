<?php

namespace App\Policies;

use App\Policies\Traits\HandlesTeamAuthorization;
use Illuminate\Auth\Access\HandlesAuthorization;
use Musonza\Chat\Models\Message;

class MessagePolicy
{
    use HandlesAuthorization, HandlesTeamAuthorization;

    public function update($user, Message $message)
    {
        if ($message->participation->messageable->is($user)) {
            return true;
        }
    }

    public function delete($user, Message $message)
    {
        if ($message->participation->messageable->is($user)) {
            return true;
        }
    }
}
