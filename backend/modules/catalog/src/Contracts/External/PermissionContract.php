<?php

namespace Modules\Catalog\Contracts\External;

/**
 * Outgoing port for permission checks used by the Catalog module.
 *
 * This abstracts the permission system so the module doesn't depend
 * on a specific implementation (e.g., Spatie Permission).
 */
interface PermissionContract
{
    /**
     * Determine if the current authenticated account has the given permission.
     */
    public function allows(string $permission): bool;
}
