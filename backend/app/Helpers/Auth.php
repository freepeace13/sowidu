<?php

namespace App\Helpers;

use App\Models\User;

class Auth
{
    public function companyCheck()
    {
        return !is_null($this->employeeUserId()) && auth()->guard('commercial')->check();
    }

    public function company()
    {
        return $this->companyCheck() ? auth()->guard('commercial')->user() : null;
    }

    public function user()
    {
        return !is_null($this->company())
            ? User::findOrFail($this->employeeUserId())
            : auth()->user();
    }

    public function employee()
    {
        if (is_null($this->company())) {
            return null;
        }

        return $this->company()->getEmployee($this->user());
    }

    public function employeeUserId()
    {
        return request()->headers->get('X-Primary-Id', null);
    }

    public function guard()
    {
        foreach (config('auth.guards') as $key => $guard) {
            if (auth()->guard($key)->check() && $key !== 'api') {
                return $key;
            }
        }

        return auth()->getDefaultDriver();
    }
}
