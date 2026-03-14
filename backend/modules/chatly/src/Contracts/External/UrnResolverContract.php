<?php

namespace Modules\Chatly\Contracts\External;

/**
 * Outgoing port for URN (Uniform Resource Name) resolution.
 *
 * The main application's Urn package provides the adapter.
 */
interface UrnResolverContract
{
    /**
     * Resolve a URN string to its corresponding entity.
     *
     * @param  string  $urn  URN string (e.g., 'urn:user:42', 'urn:team-membership:9')
     * @return mixed The resolved entity
     *
     * @throws \InvalidArgumentException If URN is invalid or not found
     */
    public function resolve(string $urn): mixed;

    /**
     * Generate a URN string from an entity.
     *
     * @param  mixed  $entity  The entity to convert to URN
     * @return string The URN string
     */
    public function generate(mixed $entity): string;

    /**
     * Validate if a URN string is valid.
     */
    public function isValid(string $urn): bool;

    /**
     * Parse a URN into its components.
     *
     * @return array ['type' => 'user', 'id' => 42]
     */
    public function parse(string $urn): array;
}
