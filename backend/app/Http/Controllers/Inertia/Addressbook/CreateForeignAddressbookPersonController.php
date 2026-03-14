<?php

namespace App\Http\Controllers\Inertia\Addressbook;

use App\Actions\Addressbook\Person\CreatesForeignPersonAddressbook;
use App\Http\Controllers\Inertia\InertiaController;
use Illuminate\Http\Request;

class CreateForeignAddressbookPersonController extends InertiaController
{
    public function __invoke(Request $request, CreatesForeignPersonAddressbook $creator)
    {
        $creator->create($request->user(), $request->all(), $this->getCurrentTeamId());

        return back(303);
    }
}
