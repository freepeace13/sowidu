<?php

namespace App\Actions\AppSettings\User;

use App\Actions\Traits\AsAction;
use App\Enums\Permissions;
use App\Models\User;

class BlockUser
{
    use AsAction;

    public function handle(User $user, User $userToBlock)
    {
        abort_unless(Permissions::isSuperAdmin($user), 403);

        $userToBlock->forceFill([
            'blocked_access' => true,
        ])
            ->save();
    }
}
