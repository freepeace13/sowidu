<?php

namespace App\Http\Controllers\Inertia\Auth;

use App\Traits\WithSnackbar;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ResetPasswordController extends Controller
{
    use WithSnackbar;

    public function showResetForm(Request $request)
    {
        $token = $request->route()->parameter('token');

        return Inertia::render('Auth/ResetPassword', [
            'title' => 'Password Reset',
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        $response = Password::broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            },
        );

        if ($response != Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [trans($response)],
            ]);
        }

        return $this->redirectSuccess('auth.login', trans($response));
    }

    protected function resetPassword($user, $password)
    {
        $user->password = bcrypt($password);

        $user->save();

        event(new PasswordReset($user));
    }

    protected function credentials(Request $request)
    {
        return $request->only(
            'email', 'password', 'password_confirmation', 'token',
        );
    }
}
