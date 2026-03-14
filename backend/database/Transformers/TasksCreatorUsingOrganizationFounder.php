<?php

namespace Database\Transformers;

use Illuminate\Support\Arr;

class TasksCreatorUsingOrganizationFounder
{
    /**
     * @return array
     */
    public function __invoke(array $item, int $key)
    {
        if (!Arr::has($item, ['organization_id', 'organization_type'])) {
            return $item;
        }

        $authorizedCreator = resolve_array_morphs([
            'id' => $item['organization_id'],
            'type' => $item['organization_type'],
        ]);

        $authorizedCreator = $authorizedCreator->founder ?? $authorizedCreator;

        $item['creator_id'] = $authorizedCreator->getKey();
        $item['creator_type'] = $authorizedCreator->getMorphClass();

        unset($item['organization_id']);
        unset($item['organization_type']);

        return $item;
    }
}
