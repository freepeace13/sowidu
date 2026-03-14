<?php

namespace App\Http\Controllers\AppSettings;

use App\Actions\AppSettings\User\BlockUser;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Transformers\PaginatorTransformer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserManagerController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when(
                $search = $request->get('search'),
                fn ($query) => $query->search($search),
            )
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('AppSettings/User/Manager', [
            'users' => collect($users->items())
                ->map(fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'email' => $user->email,
                    'verified' => $user->isVerified(),
                    'block_access' => $user->blocked_access,
                ]),
            'pagination' => PaginatorTransformer::make($users),
            'filters' => array_merge(
                [
                    'search' => '',
                ],
                $request->only(['search']),
            ),
        ]);
    }

    public function block(Request $request, User $user)
    {
        BlockUser::run($request->user(), $user);

        flash_success(__('app_settings.users-manager.messages.blocked'));

        return back();
    }
}
