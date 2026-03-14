# Chatly Module

> Agent: [.claude/agents/chatly.md](/.claude/agents/chatly.md)

Real-time chat/messaging system with conversations, participants, and messages.

## Progress Tracker

| Task | Status | Notes |
|------|--------|-------|
| External contracts | 6/6 | Complete |
| Adapters | 6/6 | All in app/Services/Chat/ |
| Tests | 3 | Needs more |
| Vite config | Pending | Create chatly.vite.mjs |
| Frontend | Complete | All in module |

**Last updated**: 2025-01-15 - Initial tracking

## Status: Partial

- [x] Service provider
- [x] External contracts (comprehensive)
- [ ] Routes need activation
- [x] Partial test coverage

## Structure

```
chatly/src/
├── Providers/ChatlyServiceProvider.php
├── Actions/
│   ├── CreateConversation.php
│   ├── UpdateConversation.php
│   ├── DeleteConversation.php
│   ├── CreateMessage.php
│   ├── DeleteMessage.php
│   ├── AddParticipant.php
│   └── RemoveParticipant.php
├── Contracts/
│   ├── CreatesConversations.php
│   ├── CreatesMessages.php
│   └── External/
│       ├── AuthorizationContract.php
│       ├── BroadcasterContract.php
│       ├── MediaManagerContract.php
│       ├── UserSearchContract.php
│       ├── UserDisplayContract.php
│       └── UrnResolverContract.php
├── Http/
│   ├── Controllers/
│   │   ├── Api/           # REST API endpoints
│   │   └── Inertia/       # Inertia controllers
│   ├── Request/           # Form requests
│   └── Resource/          # API resources
├── Models/
│   ├── Conversation.php
│   ├── Message.php
│   └── Participation.php
├── Repositories/
│   ├── ChatRepository.php
│   ├── Providers/
│   ├── Interfaces/
│   └── Services/
└── Transformers/
```

## External Contracts

This module has the most comprehensive external contracts:

| Contract | Purpose |
|----------|---------|
| `UserSearchContract` | Search users/team members |
| `UrnResolverContract` | Resolve URN identifiers |
| `MediaManagerContract` | File/media handling |
| `AuthorizationContract` | Permission checking |
| `BroadcasterContract` | Real-time events |
| `UserDisplayContract` | Avatar/display names |

See `docs/external-contracts.md` for implementation details.

## Key Patterns

### Repository Pattern
Uses providers and interfaces for data access:
```
Repositories/
├── ChatRepository.php
├── Providers/
│   ├── ConversationProvider.php
│   └── MessageProvider.php
└── Interfaces/
    └── ChatRepositoryInterface.php
```

### API Resources
Structured API responses:
```php
use Modules\Chatly\Http\Resource\ConversationResource;
use Modules\Chatly\Http\Resource\MessageResource;
```

### Real-time Broadcasting
Uses `BroadcasterContract` for WebSocket events.

## Completion Checklist

- [x] Service provider registered
- [x] External contracts comprehensive (6 contracts)
- [x] Vite alias (inline `~Chatly` in vite.config.mjs)
- [x] Repository pattern implemented
- [x] API resources defined
- [x] Transformers
- [ ] **Routes NOT active** - commented in `web.php`
- [ ] **Routes need migration** from `routes/chat.php` to module
- [ ] Vite config file (`chatly.vite.mjs`) - needs creation
- [ ] Complete test coverage
- [ ] Adapters for all 6 contracts in `app/Services/Chat/`:
  - [ ] UserSearchAdapter
  - [ ] UrnResolverAdapter
  - [ ] MediaManagerAdapter
  - [ ] AuthorizationAdapter
  - [ ] BroadcasterAdapter
  - [ ] UserDisplayAdapter

## AI Guidelines

When working here:
1. This module has the most external contracts - use them
2. Follow the repository pattern for data access
3. Use API resources for all responses
4. Check `agents.md` for orchestration patterns
5. **Priority**: Activate routes and create adapters

## Related Files
- `agents.md` - Agent orchestration patterns
- `docs/external-contracts.md` - Contract implementations
- `docs/architecture.md` - Module architecture
