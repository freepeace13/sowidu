<?php

namespace App\Http\Controllers\Inertia\Addressbook;

use App\Actions\Addressbook\Person\CreatesPersonAddressbook;
use App\Actions\Addressbook\Person\RemovesPersonAddressbook;
use App\Actions\Addressbook\Person\UpdatesPersonAddressbook;
use App\Http\Controllers\Inertia\InertiaController;
use App\Http\Controllers\Traits\AddressbookTrait;
use App\Models\Addressbook;
use App\Traits\WithOrganizationEssentials;
use App\Transformers\Addressbook\AddressbookTransformer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AddressbookPersonController extends InertiaController
{
    use AddressbookTrait, WithOrganizationEssentials;

    public function index(Request $request)
    {
        $service = $this->createServiceInstance();

        return Inertia::render('Addressbook/Person/Index', [
            'addressbooks' => $service
                ->people()
                ->matchesText($request->get('q'))
                ->orderBy('name')
                ->filter($request->only(['initial']))
                ->get()
                ->map(
                    fn ($addressbook) => (new AddressbookTransformer($addressbook))->resolve(),
                ),

            'filters' => $request->only(['initial', 'q']),
        ]);
    }

    public function show(Addressbook $person)
    {
        abort_if($person->isOrganization(), 'Cannot find this person on your address book.');

        return Inertia::render('Addressbook/Person/Show', [
            'addressbook' => (new AddressbookTransformer($person))
                ->withAddress()
                ->withCareOfs()
                ->resolve(),
            'organizations' => $person->organizations()
                ->get()
                ->map(
                    fn ($organization) => (new AddressbookTransformer($organization))
                        ->withOrganizationMembership($organization)
                        ->resolve(),
                ),
            'positions' => Inertia::lazy(fn () => $this->allPositions()),
        ]);
    }

    public function store(Request $request, CreatesPersonAddressbook $creator)
    {
        $creator->create($request->user(), $request->all(), $this->getCurrentTeamId());

        return back(303);
    }

    public function update(
        Request $request,
        Addressbook $person,
        UpdatesPersonAddressbook $updater,
    ) {
        $updater->update(
            $request->user(),
            $person,
            $request->all(),
            $this->getCurrentTeamId(),
        );

        return back(303);
    }

    public function destroy(Request $request, Addressbook $person, RemovesPersonAddressbook $remover)
    {
        $remover->remove($request->user(), $person, $this->getCurrentTeamId());

        return back(303);
    }
}
