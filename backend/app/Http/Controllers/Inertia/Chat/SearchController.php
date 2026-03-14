<?php

namespace App\Http\Controllers\Inertia\Chat;

use App\Http\Controllers\Inertia\InertiaController;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends InertiaController
{
    public function index(Request $request)
    {
        $keyword = $request->query('keyword');

        $people = User::search($keyword)
            ->unless($this->isImpersonating(), function ($query) {
                $query->where('id', '!=', $this->user()->getKey());
            })
            ->when($this->isImpersonating(), function ($query) {
                $query->where('id', '!=', $this->user()->user_id);
            })
            ->when($request->filled('except'), function ($query) use ($request) {
                $exceptIds = [];
                foreach ($request->except as $except) {
                    if ($except['type'] == 'App\Models\User') {
                        array_push($exceptIds, $except['id']);
                    }
                }

                return $query->whereNotIn('id', $exceptIds);
            })
            ->limit(8)
            ->get();

        $groups = Employee::search($keyword)
            ->when($this->isImpersonating(), function ($query) {
                return $query->where('user_id', '!=', $this->user()->user_id);
            }, function ($query) {
                // Not `impersonating` exclude this user
                return $query->where('user_id', '!=', $this->user()->id);
            })
            ->get()
            ->groupBy(function ($item) {
                return $item->employer->name;
            });

        return response()->json([
            'people' => $people->map(function ($person) {
                return [
                    'id' => $person->id,
                    'name' => $person->full_name,
                    'photo' => get_user_avatar_url($person),
                    'type' => get_class($person),
                    'is_user' => true,
                ];
            }),

            'groups' => $groups->map(function ($members) {
                return $members->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'name' => $member->user->full_name,
                        'photo' => get_company_avatar_url($member->employer),
                        'type' => get_class($member),
                        'role' => $member->role,
                        'is_user' => false,
                    ];
                });
            }),
        ]);
    }
}
