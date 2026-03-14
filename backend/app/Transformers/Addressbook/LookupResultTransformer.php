<?php

namespace App\Transformers\Addressbook;

use App\Models\Company as Team;
use App\Models\Employee as TeamMember;
use App\Models\User as Person;
use App\Transformers\Transformer;
use Packages\Urn\UrnManager;

class LookupResultTransformer extends Transformer
{
    public function toArray($request)
    {
        $transformer = match (get_class($this->resource)) {
            Person::class => $this->personTransformer(),
            Team::class => $this->teamTransformer(),
            TeamMember::class => $this->teamMemberTransformer(),
        };

        $transformedProps = $transformer($this->resource);

        return array_merge([
            'urn' => UrnManager::generate($this->resource),
        ], $transformedProps);
    }

    public function personTransformer()
    {
        return function ($resource) {
            return [
                'name' => $resource->fullName,
                'photo' => get_user_avatar_url($resource),
            ];
        };
    }

    public function teamTransformer()
    {
        return function ($resource) {
            return [
                'name' => $resource->name,
                'photo' => get_company_avatar_url($resource),
            ];
        };
    }

    public function teamMemberTransformer()
    {
        return function ($resource) {
            return [
                'name' => $resource->fullName,
                'jobtitle' => 'Dummy Job Title',
                'photo' => get_user_avatar_url($resource),
                'team' => ($this->teamTransformer())($resource->employer),
            ];
        };
    }
}
