<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity as ActivityBaseModel;

class Activity extends ActivityBaseModel
{
    public function reports()
    {
        return $this->hasMany(ActivityLogReport::class, 'activity_log_id');
    }
}
