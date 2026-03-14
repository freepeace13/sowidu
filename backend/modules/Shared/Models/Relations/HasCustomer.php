<?php

namespace Modules\Shared\Models\Relations;

use App\Models\Account;
use Exception;
use Illuminate\Database\Eloquent\Model;

trait HasCustomer
{
    public function setCustomer(Model $model)
    {
        $existing = Account::customers()->thatAssociatedWith($model)->first();

        if (is_null($existing)) {
            throw new Exception('Model cannot be use as customer relation');
        }

        $this->forceFill(['customer_uid' => $existing->key])->save();

        return $this;
    }

    public function getCustomerAttribute()
    {
        if (!$this->relationLoaded('customer')) {
            $this->load('customer.account');
        }

        return $this->getRelationValue('customer')->pluck('account');
    }

    public function customer()
    {
        return $this->belongsTo(Account::class, 'customer_uid', 'key');
    }
}
