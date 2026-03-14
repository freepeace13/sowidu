<?php

namespace Packages\Translation\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Packages\Translation\ActionInterface;

class SetPreferredLocale implements ActionInterface
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        $locale = $this->validateLocaleInput();

        $sessionKey = config('translation.session_key');

        App::setLocale($locale);

        $this->request->session()->put($sessionKey, $locale);

        return $locale;
    }

    protected function validateLocaleInput()
    {
        $locales = config('translation.locales');

        $validated = $this->request->validate([
            'locale' => 'required|in:' . implode(',', array_keys($locales)),
        ]);

        return $validated['locale'];
    }
}
