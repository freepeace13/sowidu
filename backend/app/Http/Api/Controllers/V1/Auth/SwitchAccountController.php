<?php

namespace App\Http\Api\Controllers\V1\Auth;

use App\Contracts\Actions\SwitchesAccount;
use Illuminate\Http\Request;
use Packages\RestApi\RestfulController;

class SwitchAccountController extends RestfulController
{
    public function __construct(
        protected SwitchesAccount $switcher,
    ) {}

    public function __invoke(Request $request)
    {
        $currentTeam = $this->switcher->switch(
            $this->api()->user(),
            $request->urn,
        );

        return $this->response([
            'currentTeam' => $currentTeam,
        ]);
    }
}
