<?php

namespace Modules\Todos\Http\Middleware;

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\Request;

class TodoHandleInertiaRequests extends HandleInertiaRequests
{
    protected $rootView = 'todos::app';

    public array $extraTranslations = ['todos'];

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'title' => trans('todos.headings.work-time-logs'),
        ]);
    }
}
