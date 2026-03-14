<?php

namespace App\Http\Controllers\Inertia\Reminders;

use App\Contracts\ReminderHandler;
use App\Http\Controllers\Inertia\InertiaController;
use App\Http\Controllers\Traits\RemindersTrait;
use Exception;
use Illuminate\Http\Request;

class FormSubmit extends InertiaController
{
    use RemindersTrait;

    public function __invoke(Request $request)
    {
        $id = $request->route('id');
        $handler = $this->getHandlerFromRequest($request);

        if (!$handler) {
            throw new Exception("Reminder handler for {$id} not configured.");
        }

        if ($handler instanceof ReminderHandler) {
            $response = $handler->handle($request, $this->getCurrentTeamId());

            $request->session()->forget($id);

            return $response;
        }
    }

    protected function getHandlerFromRequest($request)
    {
        $id = $request->route('id');
        $handlers = config('reminder.handlers', []);

        $key = str_replace('reminder:', '', $id);

        if (!array_key_exists($key, $handlers)) {
            return null;
        }

        return new $handlers[$key];
    }
}
