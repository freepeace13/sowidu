<?php

namespace App\Listeners\Addressbook;

use App\Actions\Addressbook\Person\CreatesPersonAddressbook;
use App\Events\Addressbook\AddressbookCreated;
use App\Models\Company;
use App\Services\AddressbookService;
use App\Transformers\PlaceTransformer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Packages\Urn\UrnManager;

class CreateAddressbookOnOrganizationMembers implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(AddressbookCreated $event)
    {
        if ($event->addressbook->isPerson() || $event->addressbook->isForeign()) {
            return;
        }

        $addressbook = $event->addressbook;
        $authUser = $event->user;
        $authTeam = Company::query()->findOrFail($event->teamId);

        // Company that has been added to `Addressbook`
        $company = $addressbook->source()
            ->with(['employees.user.currentPlace'])
            ->first();

        $company->employees->each(
            function ($employee) use ($addressbook, $authUser, $authTeam) {
                // Check if this addressbook already exists
                $userEmployee = $employee->user;

                // User already on the addressbook
                if (AddressbookService::make($authUser, $authTeam)->findPersonById($userEmployee->id)) {
                    return;
                }

                $memberAddressbook = (new CreatesPersonAddressbook)
                    ->allowAddressNullable()
                    ->create(
                        $authUser,
                        [
                            'urn' => UrnManager::generate($userEmployee),
                            'email' => $userEmployee->email,
                            'name' => $userEmployee->fullName,
                            'first_name' => $userEmployee->first_name,
                            'last_name' => $userEmployee->last_name,
                            'phone' => $userEmployee->mobile,
                            'photo' => get_user_avatar_url($userEmployee),
                            'address' => (new PlaceTransformer($userEmployee->currentPlace))
                                ->resolve(),
                        ],
                        $authTeam->id,
                    );

                $addressbook->organizationMembers()
                    ->attach($memberAddressbook, [
                        'position' => $employee->role,
                    ]);
            },
        );
    }
}
