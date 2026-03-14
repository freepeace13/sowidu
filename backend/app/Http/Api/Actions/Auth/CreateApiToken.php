<?php

namespace App\Http\Api\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Packages\RestApi\RestApiAction;

class CreateApiToken extends RestApiAction
{
    protected $rules = [
        'device' => 'required|string',
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];

    public function create(array $input = [], $errorBag = null)
    {
        $validated = $this->validate($input, $errorBag);

        $user = User::firstWhere('email', $validated['email']);

        if ($this->confirmPassword($user, $validated['password'])) {
            $user->tokens()
                ->where('name', $validated['device'])
                ->delete();

            return $user->createToken($validated['device']);
        }
    }

    protected function confirmPassword($user, $password)
    {
        if ($user && Hash::check($password, $user->password)) {
            return true;
        }

        $this->throwValidationError([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }
}
