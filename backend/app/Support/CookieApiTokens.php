<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CookieApiTokens
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getTokenFor(string $type)
    {
        return $this->getTokens()[$type] ?? null;
    }

    public function getTokens()
    {
        return collect(['user', 'company'])->mapWithKeys(function ($item, $key) {
            $dict = $this->getDictionary() ?? [];

            return [$item => Arr::get($dict, "auth.{$item}.accessToken")];
        })->all();
    }

    protected function getDictionary()
    {
        $cookie = urldecode($this->request->header('cookie'));
        // $cookie = str_replace('app:auth=', '', $cookie);

        $cookie = Str::between($cookie, 'app:auth=', '}; ');
        $cookie .= '}';

        return json_decode($cookie, true);
    }
}
