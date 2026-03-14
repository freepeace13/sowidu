<?php

namespace App\Support\ResourceTraits;

use Account;
use App\Contracts\Auth\AuthorizableGroup;
use App\Repositories\ContactRepository;
use Illuminate\Http\Resources\MissingValue;

trait LoadsContractor
{
    /**
     * @return mixed
     */
    protected function loadContractorWhenNeeded()
    {
        $contractable = Account::current();

        if ($contractable instanceof AuthorizableGroup) {
            $shouldLoad = !$contractable->is($this->resource->contractor);

            return $this->when($shouldLoad, function () {
                return $this->loadContractor();
            });
        }

        return new MissingValue;
    }

    /**
     * @return mixed
     */
    protected function loadContractor()
    {
        return $this->when(!is_null($this->resource->contractor), function () {
            return ContactRepository::resolveContactableResource(
                $this->resource->contractor->impersonated,
            );
        });
    }
}
