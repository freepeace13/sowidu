<?php

namespace App\Http\Middleware\Reminders;

use App\Support\Facades\Impersonate;

final class SetupAccountAddress extends ReminderMiddleware
{
    public function visible($request): bool
    {
        $user = $request->user();
        $currentTeam = Impersonate::tenant();

        return $user &&
            (($currentTeam &&
                $user->ownsTeam($currentTeam) &&
                $currentTeam->ownedPlaces->isEmpty()) ||
                $user->ownedPlaces->isEmpty());
    }

    public function render($request): array
    {
        return $this->inertia('Reminders/SetupAccountAddress', [
            'user' => $request->user(),
        ]);
    }
}
