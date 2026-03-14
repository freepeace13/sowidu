<?php

namespace App\Services\Chat;

use Illuminate\Support\Facades\Log;
use Modules\Chatly\Contracts\External\BroadcasterContract;
use ReflectionClass;

/**
 * Adapter for broadcasting real-time events.
 *
 * Wraps Laravel's broadcasting system to provide the interface
 * required by the Chatly module.
 */
class BroadcasterAdapter implements BroadcasterContract
{
    const EVENTS_DIR = '\\App\\Events\\Chat\\';

    /**
     * Broadcast an event to specific channels.
     *
     * @param  string  $event  Event name (e.g., 'MessageSent', 'MessageDeleted')
     * @param  array  $data  Event payload
     * @param  array  $channels  Channel identifiers
     */
    public function broadcast(string $event, array $data, array $channels): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $eventClass = $this->resolveEventClass($event);

        if (!$eventClass) {
            return;
        }

        foreach ($channels as $channel) {
            broadcast(new $eventClass($channel, $data))->toOthers();
        }
    }

    /**
     * Broadcast to a conversation's participants.
     *
     * @param  mixed  $conversation  The conversation model
     * @param  string  $event  Event name
     * @param  array  $data  Event payload
     */
    public function broadcastToConversation(mixed $conversation, string $event, array $data): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $eventClass = $this->resolveEventClass($event);

        if (!$eventClass) {
            return;
        }

        // Broadcast to all participants
        foreach ($conversation->getParticipants() as $participant) {
            broadcast(new $eventClass($participant, $data))->toOthers();
        }
    }

    /**
     * Broadcast to a specific user.
     *
     * @param  mixed  $user  The user to broadcast to
     * @param  string  $event  Event name
     * @param  array  $data  Event payload
     */
    public function broadcastToUser(mixed $user, string $event, array $data): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $eventClass = $this->resolveEventClass($event);

        if (!$eventClass) {
            return;
        }

        broadcast(new $eventClass($user, $data))->toOthers();
    }

    /**
     * Check if broadcasting is enabled.
     */
    public function isEnabled(): bool
    {
        return config('chatly.broadcasts', false)
            || config('broadcasting.default') !== 'null';
    }

    /**
     * Resolve the event class from event name.
     */
    protected function resolveEventClass(string $event): ?string
    {
        $className = self::EVENTS_DIR . $event;

        if (!class_exists($className)) {
            // Try to instantiate using reflection
            try {
                $reflection = new ReflectionClass($className);

                return $reflection->getName();
            } catch (\ReflectionException $e) {
                Log::warning("Chat event class not found: {$className}");

                return null;
            }
        }

        return $className;
    }
}
