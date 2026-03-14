<?php

namespace Modules\Todos\Http\Controllers;

use App\Http\Controllers\Inertia\InertiaController;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Todos\Actions\SearchUser;
use Modules\Todos\Transformers\UserTransformer;

class SearchUserController extends InertiaController
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $people = (new SearchUser)->get($request);

        return response()->json([
            'results' => $people->map(fn ($person) => (new UserTransformer($person))->resolve()),
        ]);
    }

    public function show(User $user)
    {
        return response()->json((new UserTransformer($user))->withAddress()->resolve());
    }
}
