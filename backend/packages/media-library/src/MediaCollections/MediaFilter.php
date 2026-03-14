<?php

namespace Packages\MediaLibrary\MediaCollections;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Arr;

class MediaFilter extends ModelFilter
{
    const CATEGORY_ANY = 'any';

    public function category($value)
    {
        $categories = collect($value);

        if ($categories->contains('no-category')) {
            $categories = $categories
                ->reject(fn ($category) => $category === 'no-category')
                ->values();

            return $this->whereNull('category')
                ->when(
                    $categories->isNotEmpty(),
                    fn ($query) => $query
                        ->orWhereIn(
                            'category',
                            $categories->toArray(),
                        ),
                );
        }

        return $this->whereIn('category', $value);
    }

    public function customFields($values)
    {
        $this->where(function ($query) use ($values) {
            foreach ($values as $field) {
                $query->where('custom_properties->metadata->' . $field['name'], $field['value'] ?? null);
            }
        });
    }

    public function type($value)
    {
        $mimeTypes = config('media-library.mime_types', []);
        $allowedTypes = Arr::flatten($mimeTypes, 1);

        if (is_array($value)) {
            $allowedTypes = collect($mimeTypes)
                ->filter(
                    fn ($types, $key) => in_array($key, Arr::wrap($value)),
                )
                ->flatten()
                ->toArray();
        } else {
            if (array_key_exists($value, $mimeTypes)) {
                $allowedTypes = $mimeTypes[$value];
            }
        }

        $this->whereIn('mime_type', $allowedTypes);
    }

    public function address($value)
    {
        $this->whereHas(
            'addressTags',
            fn ($query) => $query->where('complete_address', 'like', "%$value%"),
        );
    }

    public function noAddress($value)
    {
        $value = boolval($value);
        if ($value) {
            return $this->doesntHave('addressTags');
        }
    }

    public function noCategory($value)
    {
        $value = boolval($value);

        if ($value) {
            return $this->whereNull('category');
        }
    }
}
