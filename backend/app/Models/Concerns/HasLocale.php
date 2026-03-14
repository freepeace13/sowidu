<?php

namespace App\Models\Concerns;

trait HasLocale
{
    public function getLocaleAttribute($value)
    {
        return $value ?? config('app.locale');
    }

    public function setLocaleAttribute($value)
    {
        if (array_key_exists($value, config('translation.locales'))) {
            $this->attributes['locale'] = $value;
        }
    }
}
