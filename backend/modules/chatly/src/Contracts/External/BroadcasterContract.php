<?php

namespace Modules\Chatly\Contracts\External;

/**
 * Outgoing port for broadcasting real-time events.
 *
 * The main application provides Laravel Broadcasting adapter.
 */
interface BroadcasterContract
{
    /**
     * Broadcast an event to specific channels.
     *
     * @param  string  $event  Event name (e.g., 'MessageSent', 'MessageDeleted')
     * @param  array  $data  Event payload
     * @param  array  $channels  Channel identifiers
     */
    public function broadcast(string $event, array $data, array $channels): void;

    /**
     * Broadcast to a conversation's participants.
     *
     * @param  mixed  $conversation  The conversation model
     * @param  string  $event  Event name
     * @param  array  $data  Event payload
     */
    public function broadcastToConversation(mixed $conversation, string $event, array $data): void;

    /**
     * Broadcast to a specific user.
     *
     * @param  mixed  $user  The user to broadcast to
     * @param  string  $event  Event name
     * @param  array  $data  Event payload
     */
    public function broadcastToUser(mixed $user, string $event, array $data): void;

    /**
     * Check if broadcasting is enabled.
     */
    public function isEnabled(): bool;
}
