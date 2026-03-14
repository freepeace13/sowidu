<?php

namespace App\Http\Controllers\Inertia\Organization;

use App\Actions\Organization\CreateRole;
use App\Actions\Organization\UpdateRole;
use App\Http\Controllers\Inertia\InertiaController;
use Illuminate\Http\Request;

class RoleController extends InertiaController
{
    public function store(Request $request)
    {
        (new CreateRole)->execute($request->user(), get_company(), $request->all());

        return back(303);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $role)
    {
        (new UpdateRole)->execute($request->user(), get_company(), $request->all());

        return back(303);
    }
}
