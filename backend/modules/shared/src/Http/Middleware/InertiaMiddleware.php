<?php

namespace Modules\Shared\Http\Middleware;

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
use Coderello\SharedData\SharedData;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Middleware;
use Packages\Translation\Facades\Translation;

class InertiaMiddleware extends Middleware
{
    /* The default permissions needed on every pages */
    protected array $defaultPermissions = [
        'access_employees',
        'update settings',
        'can access media',
        'can access chat',
        'can access todo',
        'can access order',
        'can access address book',
        'can manage address book',
        'can access work logs',
        'can access delivery tickets',
        'can access invoices',
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
            'defaults' => shared()->get('defaults'),
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

    protected function services(): array
    {
        if (blank($this->authenticator)) {
            return [];
        }

        $permissionsForImpersonatingOnly = Permissions::forImpersonatorOnly();

        return collect(AppServices::all())
            ->when(
                !Impersonate::isImpersonating(),
                fn ($collection) => $collection->reject(
                    fn ($service) => in_array(data_get($service, 'permission'), $permissionsForImpersonatingOnly),
                ),
            )
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

        // @TODO - Move this to a transformer
        $data = [
            'id' => $user->id,
            'locked' => $request->session()
                ->has(md5($user->email)),
            'name' => $user->full_name,
            'photo' => get_user_avatar_url($user),
            'username' => $user->username,
            'email' => $user->email,
            'impersonating' => Impersonate::isImpersonating(),
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

        if (Impersonate::isImpersonating()) {
            $data['tenant']['id'] = $tenant->id;
            $data['tenant']['name'] = $tenant->name;
            $data['tenant']['photo'] = get_company_avatar_url($tenant);
            $data['tenant']['is_owner'] = $user->id == $tenant->user_id;
        }

        return $data;
    }

    protected function getUserPermissions()
    {
        $gate = Gate::forUser($this->authenticator);

        if (model_is($this->authenticator, 'User')) {
            $employeePermissionsOnly = config('app.default.employee_permissions_only');

            return collect($this->defaultPermissions)
                ->merge(optional($this)->permissions ?? [])
                ->unique()
                ->mapWithKeys(fn ($permission) => [
                    $permission => !in_array($permission, $employeePermissionsOnly),
                ])
                // ->mapWithKeys(fn ($permission) => [$permission => $permission == 'access_employees' ? $gate->allows($permission) : true])
                ->toArray();
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
    }
}
