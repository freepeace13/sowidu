<?php

namespace Packages\Translation\Loaders;

use Packages\Translation\LanguageLine;
use Packages\Translation\LoaderInterface;

class DatabaseLoader implements LoaderInterface
{
    public function loadTranslations(string $locale, string $group): array
    {
        return LanguageLine::getTranslationsForGroup($locale, $group);
    }
}
