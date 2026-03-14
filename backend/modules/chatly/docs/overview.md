# Chatly Overview

Chatly powers threaded conversations between Sowidu users and internal agents. The module surfaces HTTP endpoints, actions, and events that allow other modules or services to create, manage, and archive conversations.

## Core Concepts
- **Conversation**: Top-level thread that ties messages and participants together.
- **Participant**: User or system actor taking part in a conversation.
- **Message**: Individual payload within a conversation; supports pagination and soft-deletion.

## Workflow Summary
1. A controller receives a request and validates input using `src/Http/Requests` classes.
2. The controller delegates work to one or more actions (for example, `CreateConversation` or `AddParticipant`).
3. Optional agents (see `../agents.md`) coordinate multi-step flows, such as onboarding participants and sending notifications.
4. Events are dispatched via `src/Events` to notify listeners or trigger broadcasting when enabled.

## Configuration Entry Points
- `config/chatly.php` exposes prefixes, middleware, pagination defaults, and model bindings.
- Toggle broadcasting by updating the `broadcasts` flag and wiring event listeners accordingly.
- Adjust API exposure by enabling route loading through `should_load_routes`.

Document any additional features (e.g., message reactions, typing indicators) in this folder and cross-link new files from here so future readers have a single starting point.
