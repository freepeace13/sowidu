<?php

namespace App\Http\Api\Controllers\V1;

use App\Http\Api\Resources\V1\NotificationResource;
use Packages\RestApi\RestfulController;

class NotificationController extends RestfulController
{
    public function index()
    {
        $user = $this->api()->user();

        return NotificationResource::collection($user->notifications);
    }
}
