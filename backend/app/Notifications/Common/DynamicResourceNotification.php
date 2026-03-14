<?php

namespace App\Notifications\Common;

use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DynamicResourceNotification extends AbstractNotification
{
    /**
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * @var Illuminate\Contracts\Auth\Access\Authorizable
     */
    protected $actor;

    /**
     * Create a new notification instance.
     *
     * @param  Illuminate\Database\Eloquent\Model  $model
     * @param  Illuminate\Contracts\Auth\Access\Authorizable  $actor
     * @return void
     */
    public function __construct(Model $model, Authorizable $actor)
    {
        $this->model = $model;
        $this->actor = $actor;
    }

    /**
     * {@inheritDoc}
     */
    protected function message()
    {
        return null;
    }

    /**
     * The notification metadata.
     *
     * @return array
     */
    protected function meta()
    {
        return [
            'resource' => [
                'identifier' => $this->model->getKey(),
                'type' => $this->model->getMorphClass(),
            ],
        ];
    }

    /**
     * The notification avatar.
     *
     * @return string
     */
    protected function avatar()
    {
        return $this->actor->avatar_url;
    }

    /**
     * The message string attributes
     *
     * @return array
     */
    protected function attributes()
    {
        return [
            'subject' => $this->actor->full_name,
            'resource' => Str::singular($this->model->getMorphClass()),
        ];
    }
}
