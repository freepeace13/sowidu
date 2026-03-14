<?php

namespace App\Support;

class TranslatableResolver
{
    protected $model;

    public function resolve($model)
    {
        $this->model = $model;

        foreach ($model->getTranslatableAttributes() as $key) {
            $fallback = $this->getFallbackValue($key);

            if (filled($fallback)) {
                $model->setTranslations($key, $this->translateWithFallback($key, $fallback));
            }
        }
    }

    protected function translateWithFallback($key, $fallback)
    {
        $locales = array_keys(config('translation.locales'));

        return array_reduce($locales, function ($carry, $locale) use ($key, $fallback) {
            $carry[$locale] = $this->model->hasTranslation($key, $locale)
                ? $this->model->translate($key, $locale)
                : $fallback;

            return $carry;
        }, []);
    }

    protected function getFallbackValue($key)
    {
        $translations = $this->model->getTranslations($key);

        if (filled($translations)) {
            return array_values($translations)[0];
        }
    }
}
