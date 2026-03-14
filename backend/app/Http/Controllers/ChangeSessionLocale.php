<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Packages\Translation\Actions\SetPreferredLocale;

class ChangeSessionLocale extends Controller
{
    public function __invoke(Request $request)
    {
        $locale = $this->validateLocale($request);

        $key = config('translation.session_key');

        if (Auth::check()) {
            Auth::user()->forceFill(['locale' => $locale])->save();
        }

        $request->session()->put($key, $locale);

        App::setLocale($locale);

        // (new SetPreferredLocale($request))->handle();

        return redirect()->back();
    }

    protected function validateLocale($request)
    {
        return $request->validate([
            'locale' => [
                'required',
                'in:' . implode(',', array_keys(config('translation.locales'))),
            ],
        ])['locale'];
    }
}
