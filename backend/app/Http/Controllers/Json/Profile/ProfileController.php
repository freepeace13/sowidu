<?php

namespace App\Http\Controllers\Json\Profile;

use Illuminate\Http\Request;

class ProfileController extends BaseController
{
    public function show(Request $request)
    {
        $urn = $request->query('urn');

        if (!$profile = $this->resolveFromUrn($urn)) {
            abort(404, 'Profile not found.');
        }

        return $this->createTransformerInstance($profile)
            ->withAddress()
            ->resolve();
    }
}
