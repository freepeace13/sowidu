<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Contracts\External;

/**
 * Contract for Vuetify helper operations.
 *
 * Provides helpers for creating Vuetify component options.
 */
interface VuetifyContract
{
    /**
     * Create select options from a collection.
     */
    public function createOptions(mixed $collection, string $valueKey = 'id', string $labelKey = 'name'): array;

    /**
     * Create options from enum.
     */
    public function createEnumOptions(string $enumClass): array;
}
