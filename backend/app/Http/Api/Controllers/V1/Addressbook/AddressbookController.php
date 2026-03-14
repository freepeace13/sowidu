<?php

namespace App\Http\Api\Controllers\V1\Addressbook;

use App\Actions\Addressbook\Person\CreatesPersonAddressbook;
use App\Actions\Addressbook\Person\RemovesPersonAddressbook;
use App\Actions\Addressbook\Person\UpdatesPersonAddressbook;
use App\Http\Api\Resources\V1\Addressbook\AddressbookResource;
use App\Models\Addressbook;
use App\Services\AddressbookService;
use Illuminate\Http\Request;
use Packages\RestApi\RestfulController;

class AddressbookController extends RestfulController
{
    public function index(Request $request)
    {
        return AddressbookResource::collection(
            AddressbookService::make($this->currentUser(), $this->currentTeam())
                ->matchesText($request->input('q'))
                ->get(),
            fn (AddressbookResource $resource) => $resource->withModelType(),
        );
    }

    public function store(Request $request)
    {
        $addressbook = (new CreatesPersonAddressbook)->create($this->currentUser(), $request->all(), $this->currentTeam());

        return AddressbookResource::make($addressbook)->withModelType();
    }

    public function show(Request $request, Addressbook $addressbook)
    {
        return AddressbookResource::make($addressbook)->withModelType()
            ->withAddress();
    }

    public function update(Request $request, Addressbook $addressbook)
    {
        $addressbook = (new UpdatesPersonAddressbook)->update(
            $this->currentUser(),
            $addressbook,
            $request->all(),
            $this->currentTeam(),
        );

        return AddressbookResource::make($addressbook)->withModelType();
    }

    public function destroy(Request $request, Addressbook $addressbook)
    {
        (new RemovesPersonAddressbook)->remove(
            $this->currentUser(),
            $addressbook,
            $this->currentTeam()
                ->id,
        );

        return $this->response([
            'success' => true,
        ]);
    }
}
