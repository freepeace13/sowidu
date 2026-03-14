<?php

declare(strict_types=1);

namespace App\Services\Offer;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Modules\Offer\Contracts\External\EmployeeServiceContract;

class EmployeeServiceAdapter implements EmployeeServiceContract
{
    public function find(int $id): ?Model
    {
        return Employee::find($id);
    }

    public function findOrFail(int $id): Model
    {
        return Employee::findOrFail($id);
    }

    public function getByCompany(int $companyId): Collection
    {
        return Employee::where('company_id', $companyId)->get();
    }

    public function getFromUser(Model $company, Model $user): Model
    {
        /** @var Company $company */
        /** @var User $user */
        return $company->employees()->where('user_id', $user->id)->firstOrFail();
    }
}
