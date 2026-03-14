<?php

namespace App\Http\Controllers\Json\Profile;

use App\Http\Controllers\Json\BaseController as JsonController;
use App\Models\Company as Organization;
use App\Models\User as Person;
use App\Transformers\Json\OrganizationTransformer;
use App\Transformers\Json\PersonTransformer;
use Packages\Urn\UrnManager;

class BaseController extends JsonController
{
    protected $mapTransformers = [
        Person::class => PersonTransformer::class,
        Organization::class => OrganizationTransformer::class,
    ];

    protected function resolveFromUrn($urn)
    {
        return UrnManager::resolve($urn);
    }

    protected function createTransformerInstance($resource)
    {
        return ($this->mapTransformers[get_class($resource)])::make($resource);
    }
}
