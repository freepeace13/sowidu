<?php

namespace App\Http\Controllers\Inertia\Auth;

use App\Traits\WithSnackbar;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ForgotPasswordController extends Controller
{
    use WithSnackbar;

    public function showLinkRequestForm()
    {
        return Inertia::render('Auth/ForgotPassword', [
            'title' => 'Password Recovery',
        ]);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => ['required', 'email', 'exists:users']]);

        $response = Password::broker()->sendResetLink(
            $request->only('email'),
        );

        if ($response != Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [trans($response)],
            ]);
        }

        return $this->redirectBackSuccess(trans($response));
    }
}
