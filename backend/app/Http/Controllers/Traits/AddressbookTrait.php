<?php

namespace App\Http\Controllers\Traits;

use App\Services\AddressbookService;

trait AddressbookTrait
{
    /**
     * @return \App\Models\Addressbook|AddressbookService
     */
    protected function createServiceInstance(): AddressbookService
    {
        return AddressbookService::make($this->getCurrentUser(), $this->getCurrentTeam());
    }
}
