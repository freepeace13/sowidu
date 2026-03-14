<?php

namespace App\Http\Api\Actions\User;

use App\Contracts\Actions\UpdatesUserAvatar;
use App\Events\UserProfileUpdated;
use App\Models\User;
use Packages\RestApi\RestApiAction;

class UpdateUserAvatar extends RestApiAction implements UpdatesUserAvatar
{
    protected $rules = [
        'avatar' => ['required', 'image'],
    ];

    public function update(User $user, $avatar, $errorBag = null): User
    {
        $validated = $this->validate(compact('avatar'), $errorBag);

        $user->profile->setAvatar($validated['avatar']);

        $user->refresh();

        event(new UserProfileUpdated($user));

        return $user;
    }
}
