# Database Schema

Chatly builds on top of the `musonza/chat` package, which creates four core tables. The module extends those models inside `Modules\Chatly\Models`. Below is a summary of each table and how it maps to module classes.

## `chat_conversations` → `Modules\Chatly\Models\Conversation`

| Column | Type | Notes |
| --- | --- | --- |
| `id` | BIGINT UNSIGNED, auto increment | Primary key. |
| `private` | BOOLEAN | Marks conversations as private (`true` by default). |
| `direct_message` | BOOLEAN | Indicates whether the conversation is a direct message thread. |
| `data` | TEXT, nullable | Arbitrary metadata (JSON encoded) used for display name, avatars, etc. |
| `created_at`, `updated_at` | TIMESTAMP | Standard Laravel timestamps. |

## `chat_participation` → `Modules\Chatly\Models\Participation`

| Column | Type | Notes |
| --- | --- | --- |
| `id` | BIGINT UNSIGNED, auto increment | Primary key. |
| `conversation_id` | BIGINT UNSIGNED | Foreign key to `chat_conversations.id` (cascade on delete). |
| `messageable_id` | BIGINT UNSIGNED | Morph id referencing a user/team membership. |
| `messageable_type` | STRING | Morph type for the participant model. |
| `settings` | TEXT, nullable | JSON settings (mute state, notification prefs). |
| `created_at`, `updated_at` | TIMESTAMP | Standard timestamps. |
| Unique index | (`conversation_id`, `messageable_id`, `messageable_type`) | Prevents duplicate participants in the same conversation. |

## `chat_messages` → `Modules\Chatly\Models\Message`

| Column | Type | Notes |
| --- | --- | --- |
| `id` | BIGINT UNSIGNED, auto increment | Primary key. |
| `body` | TEXT | Message body; attachments may override semantics via `type`/`data`. |
| `conversation_id` | BIGINT UNSIGNED | Foreign key to `chat_conversations.id` (cascade on delete). |
| `participation_id` | BIGINT UNSIGNED, nullable | Foreign key to `chat_participation.id` (set null on delete). |
| `type` | STRING | Defaults to `text`; can be `image`, `attachment`, etc. |
| `data` | TEXT, nullable | JSON payload for attachments or additional metadata. |
| `created_at`, `updated_at` | TIMESTAMP | Standard timestamps. |

## `chat_message_notifications`

This table tracks per-participant read states and is accessed through `musonza/chat` internals (Chatly actions rely on the package to manage it).

| Column | Type | Notes |
| --- | --- | --- |
| `id` | BIGINT UNSIGNED, auto increment | Primary key. |
| `message_id` | BIGINT UNSIGNED | References `chat_messages.id` (cascade on delete). |
| `messageable_id` | BIGINT UNSIGNED | Morph id for the recipient. |
| `messageable_type` | STRING | Morph type for the recipient. |
| `conversation_id` | BIGINT UNSIGNED | References `chat_conversations.id` (cascade on delete). |
| `participation_id` | BIGINT UNSIGNED | References `chat_participation.id` (cascade on delete). |
| `is_seen` | BOOLEAN | Marks a message as read for the participant. |
| `is_sender` | BOOLEAN | Indicates if the participant created the message. |
| `flagged` | BOOLEAN | Allows flagging messages for moderation. |
| `created_at`, `updated_at`, `deleted_at` | TIMESTAMP | Standard timestamps with soft deletes. |
| Index | (`participation_id`, `message_id`) | Optimizes lookups for read/unread calculations. |

> **Note:** If you extend the schema (custom columns or additional tables), document the changes here and update the related Eloquent models and resources to expose the new data where appropriate.
