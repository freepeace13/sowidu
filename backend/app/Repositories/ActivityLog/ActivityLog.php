<?php

namespace App\Repositories\ActivityLog;

use App\Models\Employee;
use App\Models\User;
use App\Repositories\ActivityLog\Models\Board;
use App\Repositories\ActivityLog\Models\BoardSubscriber;
use App\Repositories\ActivityLog\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\Facades\CauserResolver;

/**
 * Log Model activity
 *
 * @method Board board()
 * @method Task task()
 * @method BoardSubscriber boardSubscriber()
 * @method \App\Repositories\ActivityLog\Models\OrderLog orderLog()
 */
class ActivityLog
{
    public function __construct(
        protected Model $model,
        protected User|Employee|null $causer = null,
    ) {
        CauserResolver::setCauser($this->causer ?? auth_user());
    }

    public function __get($name): mixed
    {
        return $this->make($name);
    }

    public function __call($name, $arguments): mixed
    {
        if (!in_array($name, get_class_methods(get_class()))) {
            return $this->{$name};
        }

        return null;
    }

    public function make($provider): mixed
    {
        $class = $this->resolveClassPath($provider);

        return new $class($this->model);
    }

    protected function resolveClassPath($provider): string
    {
        return 'App\\Repositories\\ActivityLog\\Models\\' . Str::studly($provider);
    }
}
