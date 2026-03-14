<?php

namespace App\Transformers;

/**
 * @property \App\Models\Category $resource
 */
class CategoryTransformer extends Transformer
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'is_default' => $this->resource->is_default,
        ];
    }

    public function withAutoShareSettings()
    {
        // 'is_enabled_on_media_settings' => true,
        return $this->state(function (array $attributes) {
            return [
                'settings' => [
                    'auto_share_to_roles' => $this->resource->settings()->autoShare()->all()
                        ->toArray(),
                ],
            ];
        });
    }

    public function withMediaSettingsAutoSharedToRoles(array $roles)
    {
        return $this->state(fn () => ['media_settings_auto_shared_to_roles' => $roles]);
    }
}
