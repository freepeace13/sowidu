<?php

namespace App\Http\Controllers\Inertia\Order;

use App\Actions\Order\Incoming\CreatesIncomingOrderForForeignClient;
use App\Http\Controllers\Inertia\InertiaController;
use Illuminate\Http\Request;

class CreateIncomingOrderForForeignClientController extends InertiaController
{
    public function __invoke(Request $request, CreatesIncomingOrderForForeignClient $creator)
    {
        $creator->create($request->user(), $request->all(), $this->getCurrentTeam());

        return back(303);
    }
}
