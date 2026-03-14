<?php

namespace Modules\Offer\Actions\Traits;

use Illuminate\Support\Fluent;

/**
 * Provides static factory methods for actions.
 */
trait AsAction
{
    /**
     * Create a new instance of the action via the container.
     */
    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * Create an instance and run the handle method.
     */
    public static function run(mixed ...$arguments): mixed
    {
        return static::make()->handle(...$arguments);
    }

    /**
     * Run the action if the condition is true.
     */
    public static function runIf(mixed $boolean, mixed ...$arguments): mixed
    {
        return $boolean ? static::run(...$arguments) : new Fluent;
    }

    /**
     * Run the action unless the condition is true.
     */
    public static function runUnless(mixed $boolean, mixed ...$arguments): mixed
    {
        return static::runIf(!$boolean, ...$arguments);
    }
}
