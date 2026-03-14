<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Packages\Translation\Facades\Translation;

class TranslationService
{
    public static function make(): static
    {
        return new static;
    }

    /**
     * @return mixed|\Packages\Translation\LanguageLine|\Illuminate\Database\Eloquent\Model
     */
    public function dbModel()
    {
        return resolve(config('translation.model'));
    }

    protected function getGroup(string $key): string
    {
        return Str::before($key, '.');
    }

    protected function getKey(string $key): string
    {
        return Str::after($key, "{$this->getGroup($key)}.");
    }

    public function getTranslations(string $key): array
    {
        $translations = $this->find($key);

        return data_get($translations, 'text', []);
    }

    public function find(string $key): array
    {
        $group = $this->getGroup($key);
        $key = $this->getKey($key);

        $line = $this->dbModel()
            ->where([
                'group' => $group,
                'key' => $key,
            ])
            ->first(['id', 'group', 'key', 'text']);

        if (!$line) {
            return [
                'group' => $group,
                'key' => $key,
                'text' => [
                    'de' => null,
                    'en' => Translation::lineOf($group, $key, 'en'),
                ],
            ];
        }

        $translations = collect($line->text);

        $translations->put('en', Translation::lineOf($group, $key, 'en'));

        return array_merge(
            $line->toArray(),
            [
                'text' => $translations->toArray(),
            ],
        );
    }

    public function update(string $key, ?string $text)
    {
        $model = $this->dbModel();

        return $model->updateOrCreate([
            'group' => $this->getGroup($key),
            'key' => $this->getKey($key),
        ], [
            'text' => [
                'de' => $text,
            ],
        ]);
    }

    public function all(): Collection
    {
        return collect(Arr::dot(Translation::all()))
            ->map(function ($value, $key) {
                $transKey = Str::beforeLast($key, '.');
                $locale = Str::afterLast($key, '.');

                return [
                    'key' => $transKey,
                    'locale' => $locale,
                    'value' => $value,
                ];
            })
            ->values();
    }

    public function paginate($query = [])
    {
        $perPage = 20;
        $page = LengthAwarePaginator::resolveCurrentPage('page');

        $translations = $this->all()
            ->when(
                filled($q = $query['q'] ?? null),
                function ($items) use ($q) {
                    return $items->filter(function ($item) use ($q) {
                        return Str::contains($item['key'], $q);
                    });
                },
            );

        return new LengthAwarePaginator(
            $translations->forPage($page, $perPage),
            $translations->count(),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $query,
            ],
        );
    }
}
