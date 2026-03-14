<?php

namespace App\Http\Controllers\Inertia\Reminders\Handlers;

use App\Actions\UpdatesAccountCurrentAddress;
use App\Contracts\ReminderHandler;
use Illuminate\Http\Request;

class UpdateAccountAddress implements ReminderHandler
{
    public function handle(Request $request, $teamId = null)
    {
        $input = $request->only([
            'house_number',
            'street',
            'zipcode',
            'state',
            'city',
            'country',
        ]);

        app(UpdatesAccountCurrentAddress::class)->update(
            $request->user(),
            $input,
            $teamId,
        );

        return back(303);
    }
}
