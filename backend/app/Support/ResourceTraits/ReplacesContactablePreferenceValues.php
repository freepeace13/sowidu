<?php

namespace App\Support\ResourceTraits;

use Account;
use App\Contracts\Contactable;
use App\Repositories\ContactRepository;

trait ReplacesContactablePreferenceValues
{
    /**
     * Replace the contactable resource attribute with preferenced values
     *
     * @param  mixed  $resource
     * @return array
     */
    protected function replace($resource)
    {
        $contact = $this->getContactInstance();

        if (!is_null($contact) && $contact->isConfirmed()) {
            return array_replace_recursive(value($resource), $contact->preference_data);
        }

        return value($resource);
    }

    /**
     * Conditionally replace the resource attribute values.
     *
     * @param  mixed  $resource
     * @return array
     */
    protected function replaceWhen(bool $condition, $resource)
    {
        return $condition ? $this->replace(value($resource)) : value($resource);
    }

    /**
     * Get the authorizable group contact instance of resource
     *
     * @return mixed
     */
    private function getContactInstance()
    {
        $account = Account::current();

        if (is_null($account)) {
            return $account;
        }

        return app(ContactRepository::class)->findFrom($account, $this->resource);
    }
}
