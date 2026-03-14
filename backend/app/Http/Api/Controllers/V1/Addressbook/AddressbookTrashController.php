<?php

namespace App\Http\Api\Controllers\V1\Addressbook;

use App\Actions\Addressbook\RestoreAddressbook;
use App\Http\Api\Resources\V1\Addressbook\AddressbookResource;
use App\Models\Addressbook;
use App\Services\AddressbookService;
use Illuminate\Http\Request;
use Packages\RestApi\RestfulController;

class AddressbookTrashController extends RestfulController
{
    public function index(Request $request)
    {
        return AddressbookResource::collection(
            AddressbookService::make($this->currentUser(), $this->currentTeam())
                ->matchesText($request->input('q'))
                ->onlyTrashed()
                ->get(),
            fn (AddressbookResource $resource) => $resource->withModelType(),
        );
    }

    public function restore(Request $request, $addressbook)
    {
        $addressbook = Addressbook::withTrashed()->findOrFail($addressbook);

        $addressbook = RestoreAddressbook::run(
            $this->currentUser(),
            $this->currentTeam(),
            $addressbook,
        );

        return AddressbookResource::make($addressbook)->withModelType();
    }
}
