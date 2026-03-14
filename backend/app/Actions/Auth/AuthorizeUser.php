<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthorizeUser
{
    public function authorize(array $inputs)
    {
        $validated = Validator::make($inputs, [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ])->validate();

        $user = User::whereEmail($validated['email'])->firstOrFail();

        if (!$user->hasVerifiedEmail()) {
            return false;
        }

        if (!Auth::guard('web')->attempt($validated)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        request()->session()->regenerate();
    }
}
