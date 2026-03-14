<?php

namespace App\Http\Controllers\Inertia\Account;

use App\Actions\MembershipAction;
use App\Http\Controllers\Inertia\InertiaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends InertiaController
{
    public function update(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        $user = Auth::guard('web')->user();

        $user->update(
            array_merge($request->only(['first_name', 'last_name']), [
                'password' => bcrypt($request->password),
            ]),
        );

        (new MembershipAction)->accept($user);

        return back(303);
    }
}
