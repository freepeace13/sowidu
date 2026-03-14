<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Contracts\External;

/**
 * Contract for Inertia middleware operations.
 *
 * Provides shared Inertia data.
 */
interface InertiaContract
{
    /**
     * Get shared data for Inertia responses.
     */
    public function getSharedData(): array;

    /**
     * Get the root view for Inertia.
     */
    public function getRootView(): string;
}
