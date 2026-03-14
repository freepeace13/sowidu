<?php

namespace App\Http\Controllers\Inertia;

use App\Models\CompanyInvitation as Invitation;
use App\Models\User;
use App\Support\Facades\Impersonate;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;

class EmployeeController extends InertiaController
{
    public function users(Request $request)
    {
        $keyword = $request->query('search') ?? '';

        $organization = Impersonate::tenant();

        $users = User::query()
            ->search($keyword)
            ->whereDoesntHave('employments', function ($query) use ($organization) {
                $query->where('company_id', $organization->getKey());
            })
            ->get();

        $users = $users->filter(function ($user) use ($organization) {
            return !Invitation::pending()
                ->whereEmail($user->email)
                ->whereCompanyId($organization->getKey())
                ->exists();
        });

        if ($this->shouldRespondJson()) {
            return response()->json([
                'users' => $users->map(fn ($user) => (new UserTransformer($user))->resolve()),
            ]);
        }
    }

    protected function withParams(array $params)
    {
        return array_merge([
            'title' => 'Employees',
        ], $params);
    }
}
