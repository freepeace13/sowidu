<?php

namespace Modules\Company\Models;

use App\Models\Foundations\Organization;
use App\Models\User;
use App\Support\Facades\Impersonate;
use App\Support\Organization\OrganizationSettings;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Keygen\Keygen;
use Modules\Shared\Models\Concerns;
use Modules\Shared\Models\Relations;
use TaylorNetwork\UsernameGenerator\Generator;

class Company extends Organization
{
    use Concerns\HasContacts,
        Concerns\HasPlaces,
        Concerns\HasProfile,
        Relations\HasAvatar,
        Relations\HasCategories,
        Relations\HasOrders,
        Relations\Searchable;

    const FOUNDER_ROLE_NAME = 'Founder';

    protected $searchable = [
        'columns' => ['name'],
        'relations' => ['user'],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'user_id',
        'confirmed',
        'active_status',
        'legal_form_id',
        'institution_type_id',
        'uuid',
        'settings',
        'vat_identification_number',
        'tax_number',
        'currency',
        'iban',
        'bic',
        'bank_name',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'settings' => 'array',
    ];

    /**
     * Holds guard name.
     *
     * @var string
     */
    protected $guard_name = 'commercial';

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Keygen::alphanum(20)->generate();

            if (empty($model->username)) {
                $model->username = (new Generator)->generateFor($model);
            }
        });

        static::created(function (self $company) {
            $company->settings()
                ->saveDefaults();
        });
    }

    public function guardName()
    {
        return 'commercial';
    }

    public function settings(): OrganizationSettings
    {
        return new OrganizationSettings($this);
    }

    /**
     * @return \App\Models\Employee
     */
    public function getFounderAttribute()
    {
        return $this->employees()
            ->whereUserId($this->user->id)
            ->first();
    }

    /**
     * Find company name.
     *
     * @param  \Illuminate\Database\Query\Builder  $query  the query builder to use
     * @param  string  $name  the name to look for
     * @return \Illuminate\Database\Query\Builder
     *
     * @author goper
     */
    public function scopeFindByName($query, $name)
    {
        $name = strtolower($name);

        return $query->where('name', 'LIKE', '%' . $name . '%');
    }

    /**
     * Get authenticated employee user ID from request header.
     *
     * @return mixed
     */
    public function getEmployeeUserId()
    {
        return request()
            ->headers
            ->get('X-Primary-Id', null);
    }

    /**
     * Get actively authenticated user in the company account.
     *
     * @return mixed
     */
    public function getAuthenticatedUser()
    {
        return $this->getEmployeeUserId()
            ? User::find($this->getEmployeeUserId())
            : null;
    }

    /**
     * Get actively authenticated employee in the company account.
     *
     * @return mixed
     */
    public function getAuthenticatedEmployee()
    {
        return $this->getAuthenticatedUser()
            ? $this->getEmployee($this->getAuthenticatedUser())
            : null;
    }

    /**
     * Determine if the user is already requested for employment.
     *
     * @param  App\Models\User  $user
     * @return bool
     */
    public function isEmploymentRequestedTo(User $user)
    {
        return $this->employmentRequests()
            ->whereCandidate($user)
            ->pending()
            ->exists();
    }

    /**
     * Check if the specified user is one of company's employee.
     *
     * @param  App\Models\User  $user
     */
    public function isEmployed(User $user): bool
    {
        return $this->employees()
            ->where('user_id', $user->id)
            ->confirmed()
            ->exists();
    }

    /**
     * Determine if the user is employed and verified in the company.
     *
     * @param  App\Models\User  $user
     */
    public function isVerifiedEmployee(User $user): bool
    {
        return $this->employees()
            ->confirmed()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Check if company has actively authenticated by multiple employees.
     *
     * @return bool
     */
    public function hasAuthenticatedUsers()
    {
        return $this->loggedBookUsers()
            ->count()
            > 0;
    }

    /**
     * Helper function that authenticate user (employee) in the company account.
     *
     * @param  App\Models\User  $user
     * @return self
     */
    public function authenticateAs(User $user)
    {
        $this->loggedBookUsers()
            ->attach($user->id);

        return $this;
    }

    /**
     * Helper function that logout user (employee) in the company account.
     *
     * @return self
     */
    public function logoutAs(User $user)
    {
        return $this->loggedBookUsers()
            ->detach($user->id);

        return $this;
    }

    /**
     * Helper function that get the employee instance based on the
     * user instance passed.
     *
     * @return \App\Models\Employee
     */
    public function getEmployee(User $user)
    {
        return $this->employees()
            ->confirmed()
            ->where('user_id', $user->id)
            ->first();
    }

    /**
     * Gets the company owner's employee card.
     *
     * @return \App\Models\Employee
     */
    public function getOwnersEmployeeCard()
    {
        return $this->getEmployee($this->user);
    }

    /**
     * Get the company's employment requests.
     */
    public function employmentRequests(): HasMany
    {
        return $this->hasMany(EmploymentRequest::class);
    }

    /**
     * Get the company's employees.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the company's legal form.
     */
    public function legalForm(): BelongsTo
    {
        return $this->belongsTo(LegalForm::class, 'legal_form_id');
    }

    /**
     * Get the company's institution type.
     */
    public function institutionType(): BelongsTo
    {
        return $this->belongsTo(InstitutionType::class, 'institution_type_id');
    }

    /**
     * Get the company's owner.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Override set attribute method of model.
     *
     * @param  string  $key
     * @param  mixed  $value
     */
    public function setAttribute($key, $value)
    {
        if ($key !== $this->getRememberTokenName()) {
            parent::setAttribute($key, $value);
        }
    }

    /**
     * Get company's logged book users.
     */
    public function loggedBookUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'company_logbook',
            'company_id',
            'user_id',
        );
    }

    public function memberships()
    {
        return $this->hasMany(Employee::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, Employee::class)
            ->withPivot('role')
            ->withTimestamps()
            ->as('membership');
    }

    public function hasUser($user)
    {
        return $this->users->contains($user);
    }

    public function hasUserWithEmail(string $email)
    {
        return $this->users->contains(function ($user) use ($email) {
            return $user->email === $email;
        });
    }

    public function roles()
    {
        return $this->morphMany(Role::class, 'model');
    }

    public function currentUserEmployment()
    {
        return $this->hasOne(Employee::class)
            ->where('user_id', Impersonate::user()?->id);
    }

    public function isFounder(User $user): bool
    {
        return $user->is($this->user);
    }

    /** @return HasMany|DeliveryTicket */
    public function deliveryTickets(): HasMany
    {
        return $this->hasMany(DeliveryTicket::class);
    }

    /** @return HasMany|CatalogItem */
    public function catalogItems(): HasMany
    {
        return $this->hasMany(CatalogItem::class);
    }

    /** @return HasMany|Invoice */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /** @return HasMany|CompanyInvitation */
    public function invitees(): HasMany
    {
        return $this->hasMany(CompanyInvitation::class);
    }

    /** @return HasMany|Tax */
    public function taxes(): HasMany
    {
        return $this->hasMany(Tax::class);
    }

    public function workLogs(): HasMany
    {
        return $this->hasMany(WorkLog::class);
    }
}
