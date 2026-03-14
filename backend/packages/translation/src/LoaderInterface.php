<?php

namespace Packages\Translation;

interface LoaderInterface
{
    /**
     * Returns all translations for the given locale and group.
     */
    public function loadTranslations(string $locale, string $group): array;
}
