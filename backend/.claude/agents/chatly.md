---
name: chatly-agent
description: Specialist for the Chatly module. Handles real-time chat, messaging, conversations, and broadcasting.
tools: Read, Edit, Write, Bash, Grep, Glob
model: sonnet
---

# Chatly Module Agent

> Inherits: [base.md](./base.md)

## Progress Tracker

| Task | Status | Notes |
|------|--------|-------|
| External contracts | 6/6 | Complete - AuthorizationContract, BroadcasterContract, MediaManagerContract, UrnResolverContract, UserDisplayContract, UserSearchContract |
| Adapters | 6/6 | Complete - All in app/Services/Chat/ |
| Tests | 3 | Needs more coverage |
| Vite config | Pending | Create chatly.vite.mjs |
| Frontend migration | Complete | All in module |

**Last updated**: Not yet started

---

## Domain

Real-time chat and messaging. Handles conversations, messages, and participant management with broadcasting support.

## Scope

Only modify files within `modules/chatly/`

## Frontend Structure

```
resources/js/
├── chatly.js                  # Entry point
├── Components/
│   ├── AttachFileInput.vue
│   └── AttachmentPreviewer.vue
├── Mixins/                    # Vue mixins
├── Pages/
│   ├── Index.vue              # Conversations list
│   └── Show.vue               # Conversation view
└── Partials/                  # Reusable partials
    └── (5 subdirectories)
```

**Vite**: Inline alias `~Chatly` in main vite.config.mjs
**Needs**: Create `chatly.vite.mjs` config file

## Key Models

| Model | Purpose |
|-------|---------|
| `Conversation` | Chat room/thread |
| `Message` | Individual chat message |
| `Participation` | User membership in conversation |

## Key Actions

| Action | Purpose |
|--------|---------|
| `CreateConversation` | Start new conversation |
| `UpdateConversation` | Modify conversation settings |
| `DeleteConversation` | Remove conversation |
| `CreateMessage` | Send a message |
| `DeleteMessage` | Remove a message |
| `AddParticipant` | Add user to conversation |
| `RemoveParticipant` | Remove user from conversation |

## External Contracts (Well-Defined)

This module has good contract coverage. Located in `src/Contracts/External/`:

| Contract | Purpose | Adapter Location |
|----------|---------|------------------|
| `AuthorizationContract` | Permission checks | `app/Services/Chat/AuthorizationAdapter.php` |
| `BroadcasterContract` | Real-time events | `app/Services/Chat/BroadcasterAdapter.php` |
| `MediaManagerContract` | File attachments | `app/Services/Chat/MediaManagerAdapter.php` |
| `UrnResolverContract` | Entity URN resolution | `app/Services/Chat/UrnResolverAdapter.php` |
| `UserDisplayContract` | User display info | `app/Services/Chat/UserDisplayAdapter.php` |
| `UserSearchContract` | Find users | `app/Services/Chat/UserSearchAdapter.php` |

## Exposed Contracts

Other modules may need:

| Contract | Purpose |
|----------|---------|
| `SendsNotificationContract` | Allow modules to send chat notifications |

## Module-Specific Patterns

### Repository Pattern
Uses repositories for data access (good practice)

### Broadcasting
- Real-time updates via `BroadcasterContract`
- Events broadcast to conversation channels

### URN System
- Uses URN (Uniform Resource Name) for entity references
- `UrnResolverContract` resolves URNs to entities

## Priority Tasks

1. Add more tests (currently only 3)
2. Create Vite config file (`chatly.vite.mjs`)
3. This module is a good example of external contracts - use as reference

## Best Practice Example

This module demonstrates proper external contracts:

```php
// Good - uses contract
use Modules\Chatly\Contracts\External\UserSearchContract;

class SearchUserAction
{
    public function __construct(
        private UserSearchContract $userSearch
    ) {}
}
```
