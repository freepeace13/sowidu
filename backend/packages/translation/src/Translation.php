<?php

namespace Packages\Translation;

class Translation
{
    public static function locales()
    {
        return config('translation.locales');
    }

    public static function loaders()
    {
        return config('translation.loaders');
    }
}
