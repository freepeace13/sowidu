<?php

namespace App\Http\Controllers\Inertia\Addressbook;

use App\Actions\Addressbook\Organization\Member\AddressbookAddOrganizationMember;
use App\Actions\Addressbook\Organization\Member\AddressbookRemoveOrganizationMember;
use App\Actions\Addressbook\Organization\Member\AddressbookUpdateOrganizationMember;
use App\Http\Controllers\Inertia\InertiaController;
use App\Models\Addressbook;
use Illuminate\Http\Request;

class AddressbookOrganizationMemberController extends InertiaController
{
    public function store(
        Request $request,
        Addressbook $organization,
        AddressbookAddOrganizationMember $adder,
    ) {
        $adder->add(
            $request->user(),
            $organization,
            $request->all(),
            $this->getCurrentTeamId(),
        );

        return back(303);
    }

    public function update(
        Request $request,
        Addressbook $organization,
        Addressbook $member,
        AddressbookUpdateOrganizationMember $updater,
    ) {
        $updater->update(
            $request->user(),
            $organization,
            $member,
            $request->all(),
            $this->getCurrentTeamId(),
        );

        return back(303);
    }

    public function destroy(
        Request $request,
        Addressbook $organization,
        Addressbook $member,
        AddressbookRemoveOrganizationMember $remover,
    ) {
        $remover->remove($request->user(), $organization, $member, $this->getCurrentTeamId());

        return back(303);
    }
}
