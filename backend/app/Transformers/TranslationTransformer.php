<?php

namespace App\Transformers;

class TranslationTransformer extends Transformer
{
    public function toArray($request)
    {
        return [
            'key' => $this->resource['key'],
            'locale' => $this->resource['locale'],
            'value' => $this->resource['value'],
        ];
    }

    public function withOtherTranslations(array $translations): self
    {
        return $this->state(
            fn ($attributes) => collect($translations)
                ->merge($attributes)
                ->filter(fn ($val, $locale) => $locale !== 'en')
                ->toArray(),
        );
    }
}
