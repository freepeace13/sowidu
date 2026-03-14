<?php

namespace App\Http\Controllers\Inertia\Organization;

use App\Actions\RegistersCompany;
use App\Models\Company;
use App\Models\CompanyInvitation as Invitation;
use App\Models\InstitutionType;
use App\Models\LegalForm;
use App\Support\Facades\Impersonate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class OrganizationController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $user = Impersonate::user();

        $invitations = Invitation::pending()->whereEmail($user->email)
            ->get();

        $organizations = Company::query()
            ->whereHas('employees', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();

        return Inertia::render('Account/Organizations', [
            'title' => 'Organizations',

            'organizations' => $organizations->map(function ($organization) {
                return [
                    'id' => $organization->id,
                    'uuid' => $organization->uuid,
                    'name' => $organization->name,
                    'photo' => $organization->avatar_url,
                ];
            }),

            'invitations' => $invitations->map(function ($invitation) {
                return [
                    'id' => $invitation->id,
                    'sent_at' => $invitation->created_at->diffForHumans(),
                    'note' => $invitation->note,
                    'company' => [
                        'id' => $invitation->company->id,
                        'name' => $invitation->company->name,
                        'photo' => $invitation->company->avatar_url,
                    ],
                ];
            }),

            'institutionTypes' => InstitutionType::all()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->type,
                    'abbrev' => $item->abbreviation,
                ];
            }),

            'legalForms' => LegalForm::all()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->legal_form,
                    'abbrev' => $item->abbreviation,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $validated = $request->validate([
                'name' => ['required', 'string', 'min:5', 'unique:companies'],
                'institution_type' => ['required', 'exists:institution_types,id'],
                'legal_form' => ['required', 'exists:legal_forms,id'],
            ]);

            app(RegistersCompany::class)->register(Impersonate::user(), [
                'name' => $validated['name'],
                'institution_type' => $validated['institution_type'],
                'legal_form' => $validated['legal_form'],
            ]);

            return Redirect::route('desktop');
        });
    }

    public function authorize(Request $request)
    {
        $token = $request->query('token');

        $company = Company::whereUuid($token)->firstOrFail();

        $this->authorizeForUser(Impersonate::user(), 'impersonate', $company);

        $request->user()
            ->switchTeam($company);

        return Inertia::location(route('desktop'));
    }

    public function logout(Request $request)
    {
        $request->user()
            ->switchTeam(null);

        return Inertia::location(route('desktop'));
    }
}
