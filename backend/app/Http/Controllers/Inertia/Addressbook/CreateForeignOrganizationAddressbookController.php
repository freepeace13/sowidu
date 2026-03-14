<?php

namespace App\Http\Controllers\Inertia\Addressbook;

use App\Actions\Addressbook\Person\CreatesForeignOrganizationAddressbook;
use App\Http\Controllers\Inertia\InertiaController;
use Illuminate\Http\Request;

class CreateForeignOrganizationAddressbookController extends InertiaController
{
    public function __invoke(Request $request, CreatesForeignOrganizationAddressbook $creator)
    {
        $creator->create($request->user(), $request->all(), $this->getCurrentTeamId());

        return back(303);
    }
}
