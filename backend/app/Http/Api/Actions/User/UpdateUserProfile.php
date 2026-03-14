<?php

namespace App\Http\Api\Actions\User;

use App\Contracts\Actions\UpdatesUserProfile;
use App\Enums\Gender;
use App\Events\UserProfileUpdated;
use App\Models\User;
use Packages\RestApi\RestApiAction;

class UpdateUserProfile extends RestApiAction implements UpdatesUserProfile
{
    public function getValidationRules(): array
    {
        $genders = array_values(Gender::getConstants());

        return [
            'firstName' => ['required', 'string'],
            'lastName' => ['required', 'string'],
            'birthdate' => ['required', 'date'],
            'gender' => ['required', 'in:' . implode(',', $genders)],
        ];
    }

    public function update(User $user, array $data, $errorBag = null): User
    {
        $validated = $this->validate($data, $errorBag);

        $user->update([
            'first_name' => $validated['firstName'],
            'last_name' => $validated['lastName'],
        ]);

        $user->profile()->update([
            'birthdate' => $validated['birthdate'],
            'gender' => $validated['gender'],
        ]);

        $user->refresh();

        event(new UserProfileUpdated($user));

        return $user;
    }
}
