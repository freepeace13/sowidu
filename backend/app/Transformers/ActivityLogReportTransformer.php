<?php

namespace App\Transformers;

use App\Models\User;

/**
 * @property \App\Models\ActivityLogReport $resource
 */
class ActivityLogReportTransformer extends Transformer
{
    public function __construct($resource)
    {
        parent::__construct(...func_get_args());
    }

    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'work_log_id' => $this->resource->work_log_id,
            'activity_log_id' => $this->resource->activity_log_id,
            'user_id' => $this->resource->user_id,
            'note' => $this->resource->note,
            'share_to_client' => $this->resource->share_to_client,
            'created_at' => $this->resource->created_at,
        ];
    }

    public function withUser(User $causer)
    {
        return $this->state(fn ($attr) => [
            'user' => (new UserTransformer($causer))
                ->resolve(),
        ]);
    }
}
