<?php

namespace App\Support;

use App\Contracts\ArrayFormatter;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TranslationFormatter implements ArrayFormatter
{
    public function format(array $values = [])
    {
        $results = [];

        foreach ($values as $group => $lines) {
            foreach (Arr::dot($lines) as $key => $text) {
                $locale = Str::afterLast($key, '.');

                $entryKey = Str::beforeLast($key, ".{$locale}");

                data_fill($results, "{$locale}.{$group}.{$entryKey}", $text);
            }
        }

        return $results;
    }
}
