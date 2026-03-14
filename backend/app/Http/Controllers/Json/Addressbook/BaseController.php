<?php

namespace App\Http\Controllers\Json\Addressbook;

use App\Http\Controllers\Json\BaseController as JsonController;
use App\Http\Controllers\Traits\AddressbookTrait;
use App\Traits\InteractsWithImpersonator;

class BaseController extends JsonController
{
    use AddressbookTrait, InteractsWithImpersonator;
}
