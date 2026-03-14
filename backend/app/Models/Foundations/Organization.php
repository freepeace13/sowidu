<?php

namespace App\Models\Foundations;

use App\Contracts\Auth\AuthorizableGroup;
use App\Contracts\Business\Billerable as BillerableContract;
use App\Contracts\Contactable as ContactableContract;
use App\Models\Company;
// use App\Models\Relations\Addressable;
use App\Models\Relations\Billerable;
use App\Models\Relations\Confirmable;
use App\Models\Relations\Contactable;
use App\Models\Relations\Contractor;
use App\Models\Relations\HasActiveState;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Packages\Addressable\Traits\HasAddresses;

abstract class Organization extends Model implements AuthenticatableContract, AuthorizableGroup, BillerableContract, ContactableContract
{
    use Authenticatable,
        Billerable,
        Confirmable,
        Contactable,
        Contractor,
        // HasApiTokens,
        HasActiveState,
        HasAddresses;

    /**
     * Determine the model is an commercial account.
     *
     * @return bool
     */
    public function isCommercial()
    {
        return ($this instanceof Company) === true;
    }

    /**
     * Inverse of iscommercial method.
     *
     * @return bool
     */
    public function isNotCommercial()
    {
        return !$this->isCommercial();
    }

    /**
     * The fullname attribute getter method.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->name ?: "{$this->first_name} {$this->last_name}";
    }
}
