<?php

namespace App\Support\Models;

use Illuminate\Database\Eloquent\Model;

trait InteractsWithModelChanges
{
    /**
     * Get column changes from the `Model`
     *
     * @return array<string, array{old: string, new: string}>
     */
    public function getModelColumnChanges(Model $model): array
    {
        $rest = 'rest';

        return collect($model->getChanges())->filter(
            fn ($value, $key) => $key != 'updated_at', // Remove updated_at from the changes array
        )->mapWithKeys(function ($value, $column) use ($model) {
            return [$column => [
                'old' => $model->getOriginal($column),
                'new' => $value,
            ]];
        })->toArray();
    }
}
