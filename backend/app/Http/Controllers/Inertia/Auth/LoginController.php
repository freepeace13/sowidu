<?php

namespace App\Http\Controllers\Inertia\Auth;

use App\Actions\Auth\AuthorizeUser;
use App\Support\CookieApiTokens;
use App\Support\Facades\Impersonate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Passport\Passport;
use Laravel\Passport\TokenRepository;

class LoginController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Login', [
            'title' => 'Login',
        ]);
    }

    public function authorize(Request $request, string $accessToken)
    {
        $user = $this->getUserByToken($accessToken);

        abort_unless((bool) $user, 401);

        return Inertia::render('Authorize', [
            'title' => 'Authorization',
            'accessToken' => $accessToken,
        ]);
    }

    public function store(Request $request, AuthorizeUser $authorizer)
    {
        if (!$authorizer->authorize($request->all())) {
            return back(303)->with('flash', [
                'type' => 'error',
                'message' => 'You must verify your email address to access this page.',
                'show_resend_link' => true,
            ]);
        }

        return Inertia::location(route('desktop'));
    }

    public function destroy(Request $request)
    {
        // Impersonate::leave();
        $request->user()->switchTeam(null);

        $tokens = app(CookieApiTokens::class);

        foreach ($tokens->getTokens() as $key => $token) {
            app(TokenRepository::class)->revokeAccessToken($token);
        }

        Passport::token()->where('revoked', 1)->delete();

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Inertia::location(route('auth.login'));
    }

    private function getUserByToken(string $token)
    {
        $user = null;

        try {
            request()->headers->set('Authorization', "Bearer {$token}");
            $user = Auth::guard('personal')->user();
        } finally {
            request()->headers->remove('Authorization');
        }

        return $user;
    }
}
