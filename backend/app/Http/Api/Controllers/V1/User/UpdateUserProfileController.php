<?php

namespace App\Http\Api\Controllers\V1\User;

use App\Contracts\Actions\UpdatesUserProfile;
use App\Http\Api\Resources\V1\UserResource;
use Illuminate\Http\Request;
use Packages\RestApi\RestfulController;

class UpdateUserProfileController extends RestfulController
{
    public function __invoke(Request $request, UpdatesUserProfile $updater)
    {
        $user = $this->api()->user();

        $updated = $updater->update($user, [
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'gender' => $request->gender,
            'birthdate' => $request->birthdate,
        ]);

        return new UserResource($updated);
    }
}
