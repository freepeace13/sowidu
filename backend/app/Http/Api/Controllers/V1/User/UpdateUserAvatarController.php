<?php

namespace App\Http\Api\Controllers\V1\User;

use App\Contracts\Actions\UpdatesUserAvatar;
use App\Http\Api\Resources\V1\UserResource;
use Illuminate\Http\Request;
use Packages\RestApi\RestfulController;

class UpdateUserAvatarController extends RestfulController
{
    public function __invoke(Request $request, UpdatesUserAvatar $updater)
    {
        $user = $this->api()->user();

        $updated = $updater->update($user, $request->file('avatar'));

        return new UserResource($updated);
    }
}
