<?php

namespace App\Factories;

use App\Contracts\AccountProvider;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class Account implements AccountProvider
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Auth guard names
     *
     * @var string
     */
    const PERSONAL_GUARD_NAME = 'personal';

    const COMMERCIAL_GUARD_NAME = 'commercial';

    /**
     * @var string
     */
    const PRIMARY_HEADER = 'X-Primary-Id';

    /**
     * The guard names in array.
     *
     * @var array
     */
    public static $guards = [
        self::PERSONAL_GUARD_NAME,
        self::COMMERCIAL_GUARD_NAME,
    ];

    /**
     * @var \Illuminate\Contracts\Auth\Access\Authorizable
     */
    protected $authorizableResolver;

    /**
     * Create new factory instance.
     *
     * @param  Illuminate\Foundation\Application  $app
     */
    public function __construct(Application $app)
    {
        $this->auth = $app['auth'];
        $this->request = $app['request'];
    }

    /**
     * @return mixed
     */
    public function actingAs(Authorizable $actor, Closure $callback)
    {
        $this->authorizableResolver = function () use ($actor) {
            return $actor;
        };

        try {
            return $callback($this);
        } finally {
            $this->authorizableResolver = null;
        }
    }

    /**
     * Get the current authenticated user account.
     *
     * @return Illuminate\Contracts\Auth\Authenticatable
     */
    public function current(): ?Authenticatable
    {
        if (!$authorizable = $this->authorizable()) {
            return null;
        }

        return static::group($authorizable);
    }

    /**
     * Forward call to authorizable can method.
     *
     * @param  string  $arguments
     * @return bool
     */
    public function can(...$arguments)
    {
        return $this->authorizable()->can(...$arguments);
    }

    /**
     * Determine user is currently authenticated
     *
     * @return bool
     */
    public function check()
    {
        return !is_null($this->current());
    }

    /**
     * Determine if current logged in on company account.
     *
     * @return bool
     */
    public function checkAtCompany()
    {
        return $this->secondary() !== null;
    }

    /**
     * Determine if current logged in on private account.
     *
     * @return bool
     */
    public function checkAtPrivate()
    {
        return !$this->checkAtCompany();
    }

    /**
     * Get the current authenticated private account or user.
     *
     * @return App\Models\Auth\User
     */
    public function primary()
    {
        return ($authorizable = $this->authorizable()) instanceof Employee
            ? $authorizable->user
            : $authorizable;
    }

    /**
     * Get the current authenticated company account.
     *
     * @return App\Models\Auth\Company|null
     */
    public function secondary()
    {
        return ($authorizable = $this->authorizable()) instanceof Employee
            ? $authorizable->employer
            : null;
    }

    /**
     * Get the current authenticated employee of company account.
     *
     * @return App\Models\Auth\Employee|null
     */
    public function employee()
    {
        return ($authorizable = $this->authorizable()) instanceof Employee
            ? $authorizable
            : null;
    }

    /**
     * Get the authorizable user for current session.
     *
     * @return Illuminate\Contracts\Auth\Access\Authorizable
     */
    public function authorizable(): ?Authorizable
    {
        $customResolver = $this->authorizableResolver;

        if (is_callable($customResolver)) {
            return $customResolver();
        }

        $user = $this->auth->user();

        if ($user instanceof Company) {
            $primaryId = static::getPrimaryIdFromRequest($this->request);

            return $user->employees()
                ->where('user_id', $primaryId)
                ->first();
        }

        return $user;
    }

    /**
     * @return int|null
     */
    public function employeeId()
    {
        return optional($this->employee())->id;
    }

    /**
     * @return int|null
     */
    public static function getPrimaryIdFromRequest(Request $request)
    {
        return $request->headers->get(static::PRIMARY_HEADER, null);
    }

    /**
     * @return \App\Contracts\Auth\AuthorizableGroup
     */
    public static function group(Authorizable $authorizable)
    {
        return $authorizable->employer ?? $authorizable;
    }

    /**
     * Validate the given authenticatable user/group
     *
     * @param  mixed  $group
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public static function ensureAuthenticatesProperly(Request $request, $user)
    {
        if ($user instanceof Company) {
            $tenantId = static::getPrimaryIdFromRequest($request);
            $employment = $user->employees()->whereUserId($tenantId)->first();

            if (is_null($tenantId) || is_null($employment)) {
                throw new AuthenticationException;
            }
        }
    }
}
