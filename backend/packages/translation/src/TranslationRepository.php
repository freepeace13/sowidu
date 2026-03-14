<?php

namespace Packages\Translation;

use Illuminate\Support\Arr;
use Packages\Translation\Contracts\Repository;
use Packages\Translation\Readers\ReaderInterface;

class TranslationRepository implements Repository
{
    protected $reader;

    public function __construct(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    public function all()
    {
        return $this->reader->read();
    }

    public function exists(string $group, string $key, $locale = null)
    {
        if (!is_null($locale)) {
            return (bool) $this->lineOf($group, $key, $locale);
        }

        return Arr::has($this->lines($group), $key);
    }

    public function lines(string $group)
    {
        $results = $this->all();

        return Arr::get($results, $group, []);
    }

    public function line(string $group, string $key)
    {
        return Arr::get($this->lines($group), $key);
    }

    public function lineOf(string $group, string $key, string $locale)
    {
        return Arr::get($this->line($group, $key), $locale);
    }
}
