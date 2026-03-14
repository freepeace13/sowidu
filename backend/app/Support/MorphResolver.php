<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;

class MorphResolver
{
    public function resolve($input)
    {
        if (is_string($input)) {
            $input = explode(':', $input);
        }

        if (is_array($input)) {
            if (Arr::has($input, ['id', 'type'])) {
                $input = [$input['type'], $input['id']];
            }

            if (count($input) === 2) {
                return $this->morphsQuery($input[0], $input[1]);
            }
        }

        return null;
    }

    public function morphsQuery($aliasedModel, $identifier)
    {
        $modelClass = Relation::getMorphedModel($aliasedModel);

        return $modelClass::find($identifier);
    }
}
