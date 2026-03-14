<?php

namespace App\Http\Controllers\Inertia\Account;

use App\Actions\Organization\UpdateOrganizationProfile;
use App\Actions\UpdatesAccountCurrentAddress;
use App\Enums\Gender;
use App\Http\Controllers\Inertia\InertiaController;
use App\Http\Requests\Accounts\UpdateProfileRequest;
use App\Models\User;
use App\Services\AppServices;
use App\Support\Facades\Impersonate;
use App\Traits\WithOrganizationEssentials;
use App\Transformers\CompanyTransformer;
use App\Transformers\EmployeeTransformer;
use App\Transformers\PlaceTransformer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends InertiaController
{
    use WithOrganizationEssentials;

    public function index(Request $request)
    {
        $account = $this->getCurrentTeam() ?? $this->getCurrentUser();

        $address = $account->currentPlace;

        $data = [
            'title' => 'Profile',
            'genders' => Gender::choices(),
            'profile' => [
                'address' => (new PlaceTransformer($address))->resolve(),
            ],
        ];

        if (!Impersonate::isImpersonating()) {
            $data['profile'] = array_merge($data['profile'], [
                'first_name' => auth()->user()
                    ->first_name,
                'last_name' => auth()->user()
                    ->last_name,
                'photo' => get_user_avatar_url(auth()->user()),
                'birthdate' => isset(
                    auth()->user()
                        ->profile,
                ) && isset(
                    auth()->user()
                        ->profile->birthdate,
                )
                    ? auth()->user()
                        ->profile->birthdate->toDateString()
                    : null,
                'gender' => isset(
                    auth()->user()
                        ->profile,
                )
                    ? auth()->user()
                        ->profile->gender
                    : null,
            ]);
        } else {
            // User is `Impersonating` - get company profile too...
            $organization = Impersonate::tenant();

            // $data['profile'] = array_merge($data['profile'], [
            //     'name' => $organization->name,
            //     'photo' => get_company_avatar_url($organization),
            //     'legal_form' => $organization->legal_form_id,
            //     'institution_type' => $organization->institution_type_id,
            // ]);

            $data['profile'] = array_merge(
                $data['profile'],
                CompanyTransformer::make($organization)
                    ->withType()
                    ->withCurrentAddress($organization->currentPlace)
                    ->withInvoiceDefaults()
                    ->resolve(),
            );

            $data = array_merge($data, [
                'legalForms' => $this->legalForms(),
                'institutionTypes' => $this->institutionTypes(),
                'currencies' => AppServices::currencies(),
                'employees' => Inertia::lazy(
                    fn () => $organization->employees()
                        ->with(['user'])
                        ->get()
                        ->map(
                            fn ($employee) => EmployeeTransformer::make($employee)
                                ->withRoles()
                                ->resolve(),
                        ),
                ),
            ]);
        }

        return Inertia::render('Account/Profile', $data);
    }

    public function update(UpdateProfileRequest $request)
    {
        $account = $this->getCurrentTeam() ?? $this->getCurrentUser();

        $account->update(
            match (get_class($account)) {
                User::class => $request->only(['first_name', 'last_name']),
                default => $request->only(['name']),
            },
        );

        if ($avatar = $request->file('avatar')) {
            $account->profile->setAvatar($avatar);
        }

        if (!Impersonate::isImpersonating()) {
            $account->profile()
                ->update($request->only([
                    'birthdate',
                    'gender',
                ]));
        }

        app(UpdatesAccountCurrentAddress::class)
            ->update(
                $request->user(),
                $request->get('address', []),
                $this->getCurrentTeamId(),
            );

        if ($this->isImpersonating()) {
            UpdateOrganizationProfile::run(
                $request->user(),
                $request->company(),
                $request->only([
                    'legal_form',
                    'institution_type',
                    // 'currency',
                    // 'bank_name',
                    // 'iban',
                    // 'bic',
                    // 'website',
                    // 'managing_director',
                    // 'company_email',
                    // 'commercial_register',
                    // 'commercial_register_number',
                ]),
            );

        }

        return back(303);
    }
}
