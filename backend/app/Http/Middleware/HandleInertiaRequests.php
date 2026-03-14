<?php

namespace App\Http\Middleware;

use App\Actions\Notifications\GetCurrentUserLatestNotifications;
use App\Actions\Notifications\GetCurrentUserUnreadNotificationsCount;
use App\Enums\Gender;
use App\Enums\Permissions;
use App\Enums\UserType;
use App\Models\Company;
use App\Models\Employee;
use App\Services\AppServices;
use App\Support\Facades\Impersonate;
use App\Support\TranslationFormatter;
use App\Transformers\CompanyTransformer;
use App\Transformers\NotificationTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Middleware;
use Packages\Translation\Facades\Translation;
use Sowidu\SharedData\SharedData;

class HandleInertiaRequests extends Middleware
{
    /* The default permissions needed on every pages */
    protected array $defaultPermissions = [
        Permissions::CAN_ACCESS_EMPLOYEES,
        Permissions::CAN_ACCESS_EMPLOYEES,
        Permissions::CAN_UPDATE_SETTINGS,
        Permissions::CAN_ACCESS_MEDIA,
        Permissions::CAN_ACCESS_CHAT,
        Permissions::CAN_ACCESS_TODO,
        Permissions::CAN_ACCESS_ORDER,
        Permissions::CAN_ACCESS_ADDRESS_BOOK,
        Permissions::CAN_MANAGE_ADDRESS_BOOK,
        Permissions::CAN_ACCESS_WORK_LOGS,
        Permissions::CAN_ACCESS_DELIVERY_TICKETS,
        Permissions::CAN_ACCESS_INVOICES,
        Permissions::CAN_ACCESS_OFFERS,
    ];

    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     */
    protected $rootView = 'app';

    /**
     * @var \App\Models\User|\App\Models\Employee
     */
    protected $authenticator;

    protected $guard;

    public array $extraTranslations;

    public function __construct()
    {
        $this->guard = auth()->guard('web');

        if ($this->guard->check()) {
            $this->authenticator = Impersonate::impersonator() ?? Impersonate::user();
        }
    }

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     */
    public function share(Request $request): array
    {
        // Do this to improve performance and elevate using partial reload
        if ($partialData = $request->header('x-inertia-partial-data')) {
            $data = array_merge(
                explode(',', $partialData),
                ['flash', 'errors', 'flash_data'],
            );
            $request->headers->set('x-inertia-partial-data', implode(',', $data));
        }

        return array_merge(parent::share($request), [
            'appName' => config('app.name'),
            'defaults' => app(SharedData::class)->get('defaults'),
            'flash_data' => fn () => session()->get('flash_data'),

            'csrfToken' => csrf_token(),
            'flash' => $request->session()
                ->get('flash', []),
            'auth' => [
                'authenticated' => $this->guard->check(),
                'accessToken' => $request->session()
                    ->pull('accessToken', null),
            ],
            'locale' => fn () => app()->getLocale(),
            'locales' => fn () => config('translation.locales'),
            'dictionary' => fn () => [
                'genders' => array_flip(Gender::getConstants()),
            ],

            // @todo Temporary - separate translations and omit unused locale/translations by specific page
            'translations' => $this->getTranslations(),

            'services' => fn () => $this->services(),
        ], $this->withAuthUser($request));
    }

    protected function accountPermissions(): array
    {
        $permissions = $this->authenticator->allPermissionNames();

        if (Permissions::isSuperAdmin(Impersonate::user())) {
            $permissions = array_merge($permissions, [Permissions::SUPER_ADMIN]);
        }

        if (Impersonate::isImpersonating()) {
            $permissions = array_diff($permissions, Permissions::forPrivateUserOnly());
        }

        return $permissions;
    }

    protected function services(): array
    {
        if (blank($this->authenticator)) {
            return [];
        }

        $permissions = $this->accountPermissions();

        return collect(AppServices::all())
            ->filter(
                fn ($service) => $service['onDenied'] == 'hide' ? in_array($service['permission'], $permissions) : true,
            )
            ->map(fn ($service) => [
                ...$service,
                'disabled' => !in_array($service['permission'], $permissions),
            ])
            ->values()
            ->toArray();
    }

    protected function getTranslations(): array
    {
        return array_merge_recursive(
            app(SharedData::class)->get('translation.messages'),
            $this->getExtraTranslations(),
        );
    }

    public function getExtraTranslations(): array
    {
        if (!isset($this->extraTranslations)) {
            return [];
        }

        $locals = (new TranslationFormatter)->format(
            Arr::only(
                Translation::driver('files')->all(),
                $this->extraTranslations,
            ),
        );

        return $locals;
    }

    protected function withAuthUser(Request $request): array
    {
        if (!$this->guard->check()) {
            return ['user' => []];
        }

        return [
            'unreadNotifications' => $this->retrieveNotifications(true),
            'notifications' => Inertia::lazy(fn () => $this->retrieveNotifications()),
            'companies' => $this->retrieveCompanies(),
            'user' => $this->retrieveUser($request),
        ];
    }

    protected function retrieveUser($request)
    {
        $user = Impersonate::user();

        $tenant = optional(Impersonate::tenant());
        $impersonator = optional(Impersonate::impersonator());
        $isImpersonating = Impersonate::isImpersonating();

        // @TODO - Move this to a transformer
        $data = [
            'id' => $user->id,
            'locked' => $request->session()
                ->has(md5($user->email)),
            'name' => $user->full_name,
            'photo' => get_user_avatar_url($user),
            'username' => $user->username,
            'email' => $user->email,
            'impersonating' => $isImpersonating,
            'isPrivateUser' => !$isImpersonating,
            'impersonatorId' => $impersonator->id,
            'tenant' => [
                'id' => null,
                'name' => null,
                'photo' => null,
                'is_owner' => false,
            ],
            'authenticator' => [
                'id' => $this->authenticator->id,
                'user_type' => array_flip(UserType::getConstants())[get_class($this->authenticator)],
            ],
            'can' => $this->getUserPermissions(),
            'isGuest' => false,
        ];

        if ($isImpersonating) {
            $data['tenant']['id'] = $tenant->id;
            $data['tenant']['name'] = $tenant->name;
            $data['tenant']['photo'] = get_company_avatar_url($tenant);
            $data['tenant']['is_owner'] = $user->id == $tenant->user_id;
        }

        return $data;
    }

    // TODO: use `accountPermissions` instead!
    protected function getUserPermissions()
    {
        $gate = Gate::forUser($this->authenticator);

        if (model_is($this->authenticator, 'User')) {
            $privateUserPermissions = Permissions::forPrivateUser();

            return collect($this->defaultPermissions)
                ->merge(optional($this)->permissions ?? [])
                ->merge(...$privateUserPermissions)
                ->unique()
                ->mapWithKeys(fn ($permission) => [
                    $permission => $gate->allows($permission),
                ])
                ->toArray();

            // return collect($this->defaultPermissions)
            //     ->merge(optional($this)->permissions ?? [])
            //     ->unique()
            //     ->mapWithKeys(fn ($permission) => [
            //         $permission => !in_array($permission, $employeePermissionsOnly),
            //     ])
            //     // ->mapWithKeys(fn ($permission) => [$permission => $permission == 'access_employees' ? $gate->allows($permission) : true])
            //     ->toArray();
        }

        return array_merge(
            $this->additionalPermissions(['can access catalog']),
            [
                // Leave this permissions
                'access_employees' => $gate->allows('access', Employee::class),
                'is_super_admin' => Permissions::isSuperAdmin(Impersonate::user()),
            ],
        );
    }

    protected function additionalPermissions($permissions = []): array
    {
        return collect($this->getRequestedPermissions())
            ->merge($permissions)
            ->mapWithKeys(fn ($permission) => [
                $permission => rescue(
                    fn () => $this->authenticator->hasPermissionTo($permission),
                    false,
                    false,
                ),
            ])
            ->toArray();
    }

    protected function getRequestedPermissions(): array
    {
        return array_merge($this->defaultPermissions, optional($this)->permissions ?? []);
    }

    protected function retrieveCompanies()
    {
        return Company::query()
            ->whereHas('employees', function ($query) {
                $query->where('user_id', Impersonate::user()->id);
            })
            ->with(['currentUserEmployment'])
            ->orderBy('name')
            ->get()
            ->map(
                fn ($company) => (new CompanyTransformer($company))->withCurrentUserEmployment()
                    ->resolve(),
            );
    }

    protected function retrieveNotifications($countOnly = false)
    {
        $guard = auth()->guard('web');

        if (!$guard->check()) {
            return [];
        }

        $user = Impersonate::impersonator() ?? Impersonate::user();

        if ($countOnly) {
            return GetCurrentUserUnreadNotificationsCount::run($user);
        }

        return GetCurrentUserLatestNotifications::run($user);
        // return $user
        //     ->unreadNotifications
        //     ->map(fn ($notification) => (new NotificationTransformer($notification))->resolve());
    }
}
