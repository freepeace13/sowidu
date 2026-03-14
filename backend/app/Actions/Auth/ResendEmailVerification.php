<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Validator;

class ResendEmailVerification
{
    public function resend(array $inputs)
    {
        $validated = Validator::make($inputs, [
            'email' => [
                'required',
                'email',
                'exists:users',
            ],
        ])->validate();

        $user = User::whereEmail($validated['email'])
            ->firstOrFail(['id', 'email_verified_at', 'email']);

        if ($user->hasVerifiedEmail()) {
            return flash_error('You are already verified! Please try logging in again.');
        }

        // Re-send email verification
        $user->sendEmailVerificationNotification();

        return flash_success("We've sent an email to {$user->email}, just click on the link in that email to verify.");
    }
}
