<?php

namespace App\Transformers\Todo;

use App\Transformers\Transformer;
use App\Transformers\UserTransformer;
use Illuminate\Support\Arr;

class BoardTransformer extends Transformer
{
    public function __construct($resource)
    {
        parent::__construct(...func_get_args());
    }

    public function toArray($request)
    {
        return array_merge([
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'team_id' => $this->team_id,
            'is_predefined' => $this->isPredefined(),
        ], $this->getLogo());
    }

    protected function getLogo()
    {
        $settings = $this->settings;
        if (!Arr::has($settings, ['is_predefined', 'icon'])) {
            return ['logo' => $this->logo_path ?? config('todo.board.defaults.logo'), 'is_icon' => false];
        }

        return [
            'logo' => $this->logo_path ?? $settings['icon'] ?? 'help',
            'is_icon' => true,
            'icon_color' => $settings['icon_color'] ?? 'red',
        ];
    }

    public function withSubscribers()
    {
        return $this->state(function ($attributes) {
            return [
                'subscribers' => $this->subscribers->loadMissing(['user'])->map(function ($subscriber) {
                    return (new SubscriberTransformer($subscriber))
                        ->withUser()
                        ->resolve();
                }),
            ];
        });
    }

    public function withGroups()
    {
        return $this->state(function ($attributes) {
            return [
                'groups' => $this->settings()->groups()->orderBy('order')->all(),
            ];
        });
    }

    public function withOwner()
    {
        return $this->state(function ($attributes) {
            return [
                'owner' => (new UserTransformer($this->owner()))->resolve(),
            ];
        });
    }
}
