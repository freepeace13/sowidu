<?php

namespace App\Http\Controllers\Inertia\Addressbook;

use App\Actions\Addressbook\Organization\CreatesOrganizationAddressbook;
use App\Actions\Addressbook\Organization\RemovesOrganizationAddressbook;
use App\Actions\Addressbook\Organization\UpdatesOrganizationAddressbook;
use App\Http\Controllers\Inertia\InertiaController;
use App\Http\Controllers\Traits\AddressbookTrait;
use App\Models\Addressbook;
use App\Traits\WithOrganizationEssentials;
use App\Transformers\Addressbook\AddressbookTransformer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AddressbookOrganizationController extends InertiaController
{
    use AddressbookTrait, WithOrganizationEssentials;

    public function index(Request $request)
    {
        $service = $this->createServiceInstance();

        return Inertia::render('Addressbook/Organization/Index', [
            'organizations' => $service
                ->organizations()
                ->matchesText($request->get('q'))
                ->withCount('organizationMembers')
                ->orderBy('name')
                ->filter($request->only(['initial']))
                ->get()
                ->map(
                    fn ($organization) => (new AddressbookTransformer($organization))
                        ->withOrganizationMembersCount()
                        ->resolve(),
                ),

            // 'paginationItems' => $service->organizations()
            //     ->get('name')
            //     ->map(
            //         fn ($addressbook) => ucfirst(substr($addressbook->name, 0, 1))
            //     )->unique()->sort()->values()->toArray(),

            'filters' => $request->only(['initial', 'q']),

            'institutionTypes' => Inertia::lazy(fn () => $this->institutionTypes()),

            'legalForms' => Inertia::lazy(fn () => $this->legalForms()),
        ]);
    }

    public function store(Request $request, CreatesOrganizationAddressbook $creator)
    {
        $creator->create(
            $request->user(),
            $request->all(),
            $this->getCurrentTeamId(),
        );

        return back(303);
    }

    public function show(Addressbook $organization)
    {
        return Inertia::render('Addressbook/Organization/Show', [
            'organization' => (new AddressbookTransformer($organization))
                ->withAddress()
                ->withCareOfs()
                ->resolve(),

            'institutionTypes' => Inertia::lazy(fn () => $this->institutionTypes()),

            'legalForms' => Inertia::lazy(fn () => $this->legalForms()),

            'positions' => $this->allPositions(),

            'members' => $organization->organizationMembers()
                ->get()
                ->map(
                    fn ($memberAddressbook) => (new AddressbookTransformer($memberAddressbook))
                        ->withOrganizationMember($memberAddressbook)
                        ->resolve(),
                ),
        ]);
    }

    public function update(Request $request, Addressbook $organization, UpdatesOrganizationAddressbook $updater)
    {
        $updater->update(
            $request->user(),
            $organization,
            $request->all(),
            $this->getCurrentTeamId(),
        );

        return back(303);
    }

    public function destroy(Request $request, Addressbook $organization, RemovesOrganizationAddressbook $remover)
    {
        $remover->remove($request->user(), $organization, $this->getCurrentTeamId());

        return back(303);
    }
}
