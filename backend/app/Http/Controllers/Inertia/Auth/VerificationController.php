<?php

namespace App\Http\Controllers\Inertia\Auth;

use App\Actions\Auth\ResendEmailVerification;
use App\Actions\Auth\VerifyUser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VerificationController extends Controller
{
    public function verify(Request $request, $id, $hash, VerifyUser $verifier)
    {
        $verifier->verify($id, $hash);

        return redirect()->route('desktop');
    }

    public function resend(Request $request, ResendEmailVerification $emailVerification)
    {
        $emailVerification->resend($request->all());

        return back(303);
    }

    protected function redirectPath($accessToken)
    {
        return route('auth.authorize', [
            'accessToken' => $accessToken,
        ]);
    }
}
