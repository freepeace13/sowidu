<?php

use App\Models\Addressbook;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use App\Repositories\ActivityLog\ActivityLog;
use App\Services\AppServices;
use App\Services\CacheService;
use App\Support\ApiHelpers;
use App\Support\Facades\Impersonate;
use App\Support\MorphResolver;
use App\Support\Number;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Sowidu\SharedData\SharedData;

if (!function_exists('camel_case_array_keys')) {
    function camel_case_array_keys($array, $levels = null)
    {
        return ApiHelpers::camelCaseArrayKeys($array, $levels);
    }
}

if (!function_exists('snake_case_array_keys')) {
    function snake_case_array_keys($array, $levels = null)
    {
        return ApiHelpers::snakeCaseArrayKeys($array, $levels);
    }
}

if (!function_exists('is_email')) {
    function is_email($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}

if (!function_exists('is_multi_array')) {
    function is_multi_array($array)
    {
        if (is_array($array)) {
            foreach ($array as $value) {
                if (!is_array($value)) {
                    return false;
                }
            }
        }

        return true;
    }
}

if (!function_exists('in_array_multi')) {
    function in_multi_array($value, array $array)
    {
        if (is_array($value) && is_multi_array($array)) {
            foreach ($array as $val) {
                if (array_equals($value, $val)) {
                    return true;
                }
            }
        }

        return false;
    }
}

if (!function_exists('array_equals')) {
    function array_equals(array $array1, array $array2)
    {
        if (count($array1) !== count($array2)) {
            return false;
        }

        if (is_multi_array($array1) && is_multi_array($array2)) {
            foreach ($array1 as $key => $value) {
                if (!in_multi_array($value, $array2)) {
                    return false;
                }
            }
        } else {
            foreach ($array1 as $key => $value) {
                if (!in_array($value, $array2)) {
                    return false;
                }
            }
        }

        return true;
    }
}

if (!function_exists('array_values_diff')) {
    function array_values_diff(array $array1, array $array2)
    {
        return array_merge(
            array_diff($array2, $array1),
            array_diff($array1, $array2),
        );
    }
}

if (!function_exists('array_diff_assoc_recursive')) {
    function array_diff_assoc_recursive(array $array1, array $array2)
    {
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key])) {
                    $difference[$key] = $value;
                } elseif (!is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = array_diff_assoc_recursive($value, $array2[$key]);

                    if ($new_diff != false) {
                        $difference[$key] = $new_diff;
                    }
                }
            } elseif (!isset($array2[$key]) || ($array2[$key] != $value && !($array2[$key] === null && $value === null))) {
                $difference[$key] = $value;
            }
        }

        return !isset($difference) ? [] : $difference;
    }
}

if (!function_exists('model_alias')) {
    function model_alias(string $modelClass)
    {
        $instance = new $modelClass;

        return $instance->getMorphClass();
    }
}

if (!function_exists('resolve_array_morphs')) {
    function resolve_array_morphs(array $morphs)
    {
        return (new MorphResolver)->resolve($morphs);
    }
}

if (!function_exists('array_morphs')) {
    function array_morphs(Model $model)
    {
        return [
            'id' => $model->getKey(),
            'type' => $model->getMorphClass(),
        ];
    }
}

if (!function_exists('model_morphs_stringify')) {
    function model_morphs_stringify(Model $model)
    {
        return implode(
            '.',
            array_reverse(
                array_values(array_morphs($model)),
            ),
        );
    }
}

if (!function_exists('guess_name')) {
    function guess_name($value)
    {
        if (is_array($value)) {
            $value = (object) $value;
        }

        if (is_object($value)) {
            return $value->name ?: implode(' ', [$value->first_name, $value->last_name]);
        }

        return null;
    }
}

if (!function_exists('class_uses_trait')) {
    function class_uses_trait($class, $trait)
    {
        if (is_string($class) && !class_exists($class)) {
            return false;
        }

        $traits = class_uses_recursive($class);

        return in_array($trait, $traits);
    }
}

if (!function_exists('get_user_avatar_url')) {
    function get_user_avatar_url(User|Employee|Addressbook $user)
    {
        if ($user instanceof Addressbook) {
            return app(SharedData::class)->get('defaults.avatars.user');
        }

        $user->load('profile');

        if (!isset($user->profile)) {
            return app(SharedData::class)->get('defaults.avatars.user');
        }

        if (class_basename($user) == 'Employee') {
            $user = $user->user;
        }

        return $user->profile->avatar->getUrl();
    }
}

if (!function_exists('get_company_avatar_url')) {
    function get_company_avatar_url($company)
    {
        $defaultAvatar = app(SharedData::class)->get('defaults.avatars.company');
        $company->loadMissing(['profile']);

        return isset($company->profile) ? $company->profile->avatar->setDefaultPath($defaultAvatar)
            ->getUrl()
            : $defaultAvatar;
    }
}

if (!function_exists('get_company')) {
    /** @return \App\Models\Company|null */
    function get_company()
    {
        return Impersonate::tenant();
    }
}

if (!function_exists('auth_company')) {
    /** @return \App\Models\Company|null */
    function auth_company()
    {
        return Impersonate::tenant();
    }
}

if (!function_exists('auth_account')) {
    /** @return \App\Models\Employee|\App\Models\User|null */
    function auth_account()
    {
        $user = Impersonate::user();

        if (!$company = auth_company()) {
            return $user;
        }

        return $user->teamMembership($company);
    }
}

if (!function_exists('throw_validation')) {
    /**
     * Throw an ValidationException with the given data.
     *
     * @param  string  $message
     * @param  string  $field
     * @return never
     *
     * @throws ValidationException
     */
    function throw_validation($message = '', $field = null)
    {
        if (!$field) {
            $field = 'throw_validation_error';
        }
        throw ValidationException::withMessages([$field => $message]);
    }
}

if (!function_exists('throw_validation_if')) {
    /**
     * Throw an ValidationException with the given data if the given condition is true.
     *
     * @param  bool  $boolean
     * @param  string  $message
     * @param  string  $field
     * @return void
     *
     * @throws ValidationException
     */
    function throw_validation_if($boolean, $message = '', $field = null)
    {
        if ($boolean) {
            throw_validation($message, $field);
        }
    }
}

if (!function_exists('throw_validation_unless')) {
    /**
     * Throw an ValidationException with the given data unless the given condition is true.
     *
     * @param  bool  $boolean
     * @param  string  $message
     * @param  string  $field
     * @return void
     *
     * @throws ValidationException
     */
    function throw_validation_unless($boolean, $message = '', $field = null)
    {
        if (!$boolean) {
            throw_validation($message, $field);
        }
    }
}

if (!function_exists('throw_flash')) {
    function throw_flash(string $message = '', array $additional = [])
    {
        flash_error($message, $additional);

        throw ValidationException::withMessages([str_random(15) => $message]);
    }
}

if (!function_exists('throw_flash_if')) {
    function throw_flash_if($boolean, $message = '', array $additional = [])
    {
        if ($boolean) {
            throw_flash($message, $additional);
        }
    }
}

if (!function_exists('throw_flash_unless')) {
    function throw_flash_unless($boolean, $message = '', array $additional = [])
    {
        if (!$boolean) {
            throw_flash($message, $additional);
        }
    }
}

if (!function_exists('auth_user')) {
    /**
     * Get auth user using `Impersonate`.
     *
     * @see \App\Services\Impersonate
     */
    function auth_user(): ?User
    {
        return auth()->user();
    }
}

if (!function_exists('auth_is_private_user')) {
    function auth_is_private_user()
    {
        return Impersonate::isImpersonating() === false;
    }
}

if (!function_exists('auth_is_employee')) {
    function auth_is_employee()
    {
        return Impersonate::isImpersonating();
    }
}

if (!function_exists('deny')) {
    function deny(string $message): never
    {
        throw new AuthorizationException($message);
    }
}

if (!function_exists('deny_if')) {
    /** @throws AuthorizationException */
    function deny_if($condition, string $message)
    {
        if ($condition) {
            deny($message);
        }
    }
}

if (!function_exists('deny_unless')) {
    /** @throws AuthorizationException */
    function deny_unless($condition, string $message)
    {
        if (!$condition) {
            deny($message);
        }
    }
}

if (!function_exists('morph_is')) {
    function morph_is($morphedModel, $comparedClass)
    {
        return strtolower(class_basename($morphedModel)) === strtolower(class_basename($comparedClass));
    }
}

if (!function_exists('same_class')) {
    function same_class($class, $comparedClass)
    {
        return strtolower(class_basename($class)) === strtolower(class_basename($comparedClass));
    }
}

if (!function_exists('same_morph_alias')) {
    function same_morph_alias(string $modelClass, string $morphType): bool
    {
        return Relation::getMorphedModel($morphType) === $modelClass;
    }
}

if (!function_exists('get_morph_alias')) {
    function get_morph_alias($model)
    {
        if (is_object($model)) {
            $model = get_class($model);
        }

        $morphMap = Relation::morphMap();

        $flipMorphed = array_flip($morphMap);

        $alias = $flipMorphed[$model] ?? throw new \RuntimeException("Please add class $model to \Illuminate\Database\Eloquent\Relations\Relation::enforceMorphMap");

        return $alias;
    }
}

if (!function_exists('model_is')) {
    function model_is($model, string $modelName)
    {
        return strtolower(class_basename($model)) == strtolower($modelName);
    }
}

if (!function_exists('get_current_team')) {
    function get_current_team()
    {
        return Impersonate::tenant();
    }
}

if (!function_exists('model_name')) {
    function model_name($model)
    {
        return Str::of(class_basename($model))->snake();
    }
}

if (!function_exists('flash_success')) {
    function flash_success(string $message, array $additional = [])
    {
        session()->flash('flash', array_merge([
            'type' => 'success',
            'message' => $message,
        ], $additional));
    }
}

if (!function_exists('flash_data')) {
    function flash_data(mixed $payload)
    {
        session()->flash('flash_data', $payload);
    }
}

if (!function_exists('flash_error')) {
    function flash_error(string $message, array $additional = [])
    {
        session()->flash('flash', array_merge([
            'type' => 'error',
            'message' => $message,
        ], $additional));
    }
}

if (!function_exists('transform_array_to_lowercase')) {
    function transform_array_to_lowercase(array $array)
    {
        return array_map('strtolower', $array);
    }
}

if (!function_exists('transform_array')) {
    function transform_array(array $array, $callback = null)
    {
        return array_map($callback ?? 'strtolower', $array);
    }
}

if (!function_exists('snake_to_readable')) {
    function snake_to_readable(string $word)
    {
        return Str::of($word)
            ->title()
            ->headline()
            ->lower()
            ->ucfirst();
    }
}

if (!function_exists('is_in_array')) {
    /**
     * Case-insensitive in_array() wrapper.
     *
     * @param  mixed  $needle  Value to seek.
     * @param  array  $haystack  Array to seek in.
     * @return bool
     */
    function is_in_array(string $needle, array $haystack)
    {
        return in_array(strtolower($needle), array_map('strtolower', $haystack));
    }
}

if (!function_exists('in_iarray')) {
    function in_iarray(string $needle, array $haystack)
    {
        return is_in_array($needle, $haystack);
    }
}

if (!function_exists('array_iunique')) {
    /**
     * Case-insensitive array_unique() wrapper.
     *
     * @param  array  $array
     * @return array
     */
    function array_iunique($array)
    {
        return array_intersect_key(
            $array,
            array_unique(array_map('strtolower', $array)),
        );
    }
}

if (!function_exists('activity_log')) {
    function activity_log(Model $model, ?Model $causer = null)
    {
        return new ActivityLog($model, $causer);
    }
}

if (!function_exists('pluck_initials')) {
    function pluck_initials(string $value, string $separator = ' ', string $glue = ' '): string
    {
        $exploded = explode($separator, $value);

        return trim(
            collect($exploded)
                ->map(function ($segment) use ($exploded) {
                    $firstLetter = $segment[0];

                    if (count($exploded) === 1) {
                        $chunks = str_split($segment);

                        return $firstLetter . ucfirst($chunks[2]);
                    }

                    return $firstLetter ?? '';
                })
                ->join($glue),
        );
    }
}

if (!function_exists('convert_to_timezone')) {
    function convert_to_timezone(string $dateTime, string $timezone)
    {
        return Carbon::parse(
            $dateTime,
            $timezone,
        )
            ->setTimezone(config('app.timezone'));
    }
}

if (!function_exists('currency_symbol')) {
    function currency_symbol(?string $currency)
    {
        return AppServices::currencySymbol($currency);
    }
}

if (!function_exists('inertia_wants_data')) {
    function inertia_wants_data(string|array $keys): bool
    {
        $inertiaHeaders = request()->header('X-Inertia-Partial-Data');
        if (blank($inertiaHeaders)) {
            return true;
        }

        $partialData = explode(',', $inertiaHeaders);

        foreach ($keys as $key) {
            if (in_array($key, $partialData)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('inertia_wants_data')) {
    function inertia_wants_data(string|array $keys): bool
    {
        $inertiaHeaders = request()->header('X-Inertia-Partial-Data');
        if (blank($inertiaHeaders)) {
            return true;
        }

        $partialData = explode(',', $inertiaHeaders);

        foreach ($keys as $key) {
            if (in_array($key, $partialData)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('format_number')) {
    function format_number(
        int|float $number,
        ?int $precision = null,
        ?int $maxPrecision = null,
        ?string $locale = null,
    ): string|false {
        return Number::format($number, $precision, $maxPrecision, app()->getLocale());
    }
}

if (!function_exists('number_to_raw')) {
    function number_to_raw(string $number, ?int $precision = 2): string|false
    {
        if (!is_string($number)) {
            return (int) $number;
        }

        return str($number)->replace(',', '')
            ->toFloat();
    }
}

if (!function_exists('number_to_money')) {
    function number_to_money(int|float|string $number, string $currency = 'EUR'): string|false
    {
        if (is_string($number)) {
            $number = (float) $number;
        }

        return Number::currency(
            $number,
            $currency,
            app()->getLocale(),
        );
    }
}

if (!function_exists('format_currency')) {
    function format_currency(int|float $number, string $currency = 'EUR'): string|false
    {
        return number_to_money($number, $currency);
    }
}

if (!function_exists('data_only')) {
    function data_only(array $array, array|string $keys): array
    {
        return Arr::only($array, $keys);
    }
}

if (!function_exists('data_except')) {
    function data_except(array $array, array|string $keys): array
    {
        return Arr::except($array, $keys);
    }
}

if (!function_exists('get_company_currency')) {
    function get_company_currency(int|Company $company): string|false
    {
        return CacheService::getCompanyCurrency($company);

    }
}

if (!function_exists('get_morph_class')) {
    function get_morph_class(string $model)
    {
        return resolve($model)->getMorphClass();

    }
}

if (!function_exists('get_route_name')) {
    function get_route_name(string $url)
    {
        $routes = Route::getRoutes();

        foreach ($routes as $route) {
            if ($route->matches(request()->create($url))) {
                return $route->getName();
            }
        }

        return null; // Route not found

    }
}
