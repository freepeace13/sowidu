<?php

namespace App\Services\Chat;

use Modules\Chatly\Contracts\External\UrnResolverContract;
use Packages\Urn\UrnManager;

/**
 * Adapter for URN (Uniform Resource Name) resolution.
 *
 * Wraps the application's URN package to provide the interface
 * required by the Chatly module.
 */
class UrnResolverAdapter implements UrnResolverContract
{
    /**
     * Resolve a URN string to its corresponding entity.
     *
     * @param  string  $urn  URN string (e.g., 'urn:user:42', 'urn:team-membership:9')
     * @return mixed The resolved entity
     *
     * @throws \InvalidArgumentException If URN is invalid or not found
     */
    public function resolve(string $urn): mixed
    {
        return UrnManager::resolve($urn);
    }

    /**
     * Generate a URN string from an entity.
     *
     * @param  mixed  $entity  The entity to convert to URN
     * @return string The URN string
     */
    public function generate(mixed $entity): string
    {
        return UrnManager::generate($entity);
    }

    /**
     * Validate if a URN string is valid.
     */
    public function isValid(string $urn): bool
    {
        try {
            $this->parse($urn);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Parse a URN into its components.
     *
     * @return array ['type' => 'user', 'id' => 42]
     */
    public function parse(string $urn): array
    {
        // URN format: urn:type:id
        $parts = explode(':', $urn);

        if (count($parts) !== 3 || $parts[0] !== 'urn') {
            throw new \InvalidArgumentException("Invalid URN format: {$urn}");
        }

        return [
            'type' => $parts[1],
            'id' => $parts[2],
        ];
    }
}
