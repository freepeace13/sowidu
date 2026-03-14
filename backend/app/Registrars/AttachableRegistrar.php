<?php

namespace App\Registrars;

use App\Contracts\Attachment\Attachable as AttachableContract;
use InvalidArgumentException;

class AttachableRegistrar
{
    /**
     * The attachable model classes.
     *
     * @var array
     */
    protected $attachables = [];

    /**
     * Create new registrar instance
     */
    public function __construct(array $attachables = [])
    {
        $this->attachables = $attachables;
    }

    /**
     * Register new attachable models.
     *
     * @return void
     */
    public function register(array $models)
    {
        $this->validateAttachableModels(
            ...$this->resolveAttachableClasses($models),
        );

        foreach ($models as $doctype => $model) {
            if (!in_array($model, $this->attachables)) {
                $this->attachables[$doctype] = $model;
            }
        }
    }

    /**
     * Get the list of attachables.
     *
     * @return Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect($this->attachables);
    }

    /**
     * Validate given models if the required methods are define.
     *
     * @param  array  $attachables
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function validateAttachableModels(...$attachables)
    {
        foreach ($attachables as $model) {
            if (($model instanceof AttachableContract) === false) {
                throw new InvalidArgumentException('Attachable methods are undefined in the parental or child models.');
            }
        }

        return true;
    }

    /**
     * Initialize given attachables classes.
     *
     * @return array
     */
    private function resolveAttachableClasses(array $models)
    {
        return collect($models)->map(function ($model) {
            return new $model;
        })->all();
    }
}
