<?php

namespace App\Http\Api\Controllers\V1\Auth;

use App\Http\Api\Actions\Auth\CreateApiToken;
use Illuminate\Http\Request;
use Packages\RestApi\RestfulController;

class LoginController extends RestfulController
{
    public function __invoke(CreateApiToken $creator, Request $request)
    {
        $token = $creator->create($request->all());

        return $this->response($token->plainTextToken);
    }
}
