<?php

namespace App\Http\Controllers\Inertia;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LockScreenController extends Controller
{
    public function activate(Request $request)
    {
        $request->session()->put($this->getSessionkey(), now()->toDateTimeString());

        return back(303);
    }

    public function deactivate(Request $request)
    {
        if (Hash::check($request->password, Auth::user()->password)) {
            $request->session()->forget($this->getSessionKey());

            return back(303);
        }

        throw ValidationException::withMessages([
            'password' => 'Password is incorrect.',
        ]);
    }

    protected function getSessionKey()
    {
        return md5(Auth::user()->email);
    }
}
