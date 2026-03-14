# Chatly Features

This summary lists the core capabilities exposed by the Chatly module.

- **Conversation lifecycle** – Invokable actions under `Modules\Chatly\Actions` (for example `CreateConversation`, `UpdateConversation`, `DeleteConversation`) wrap `musonza/chat` primitives and enforce Sowidu-specific validation and authorization.
- **Participant controls** – `AddParticipant` and `RemoveParticipant` resolve URNs with `Packages\Urn\UrnManager`, guard access through Laravel gates, and deduplicate participants using the underlying `Musonza\Chat\Models\Conversation` helpers.
- **Messaging pipeline** – `CreateMessage` delivers text, images, or attachments. Repository providers (`Modules\Chatly\Repositories`) layer on pagination, attachment handling via `AttachmentBuilder`, and broadcasting with `ChatBroadcaster`.
- **HTTP transport** – API controllers (`Modules\Chatly\Http\Controllers\Api`) expose REST endpoints that return `ConversationResource`, `MessageResource`, and `ParticipantResource`, keeping responses consistent for web and mobile clients.
- **Web UI integration** – `ChatlyServiceProvider` may load module-scoped Inertia routes whose Vue components live in `modules/chatly/resources/js` and are imported with the `@Chatly` alias defined in `vite.config.mjs`.
- **Configuration hooks** – Overriding `config/chatly.php` lets the host app adjust prefixes, middleware, pagination defaults, and broadcasting support without editing module code.
