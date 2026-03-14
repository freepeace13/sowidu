<?php

namespace App\Support\Collections;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class MembersCollection extends Collection
{
    /**
     * Create a new collection instance.
     *
     * @param  array  $items
     */
    public function __construct($items = [])
    {
        parent::__construct(
            static::resolveItems($items)->all(),
        );
    }

    public function diffWith($members)
    {
        $members = static::make($members);

        return static::make(array_diff_assoc_recursive(
            $members->all(), $this->all(),
        ));
    }

    /**
     * @param  mixed  $items
     * @return \Illuminate\Support\Collection
     */
    private static function resolveItems($items = [])
    {
        return Collection::make($items)
            ->transform(function ($value) {
                if (is_array($value) && Arr::has($value, ['id', 'type'])) {
                    $value = resolve_array_morphs($value);
                }

                if (!$value instanceof Model) {
                    $value = null;
                }

                return $value;
            })
            ->filter();
    }
}
