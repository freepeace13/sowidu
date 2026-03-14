<?php

namespace App\Support\Vuetify;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class CreateOptions
{
    protected string $valueKey = 'id';

    protected string $textKey = 'name';

    public function __construct(protected $items) {}

    public static function from(Arrayable|array $items)
    {
        return new static($items);
    }

    public function setValueKey(string $valueKey): self
    {
        $this->valueKey = $valueKey;

        return $this;
    }

    public function setTextKey(string $textKey): self
    {
        $this->textKey = $textKey;

        return $this;
    }

    public function build()
    {
        $items = $this->items;

        if (!$items instanceof Collection) {
            $items = collect($items);
        }

        return $items->map(function ($item, $key) {

            if ($item instanceof \BackedEnum) {
                $name = rescue(
                    fn () => $item->trans(),
                    fn () => $item->name,
                    false,
                );

                return [
                    'value' => $item->value,
                    'text' => str($name)->replace('_', ' ')->title()->__toString(),
                ];
            }

            return [
                'value' => $item?->{$this->valueKey} ?? $item[$this->valueKey] ?? $item,
                'text' => $item?->{$this->textKey} ?? $item[$this->textKey] ?? snake_to_readable($key),
            ];
        })->toArray();
    }
}
