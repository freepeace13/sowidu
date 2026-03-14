# Chatly Architecture (Ports & Adapters)

Chatly follows the **Hexagonal Architecture** (also known as Ports and Adapters) pattern to maintain clean boundaries and high testability.

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                     INCOMING ADAPTERS                            │
│                    (Driving/Primary Side)                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌──────────────────┐        ┌──────────────────┐               │
│  │  REST API        │        │  Inertia Web     │               │
│  │  Controllers     │        │  Controllers     │               │
│  │  (Api/)          │        │  (Inertia/)      │               │
│  └────────┬─────────┘        └────────┬─────────┘               │
│           │                           │                          │
│           └───────────┬───────────────┘                          │
│                       │                                          │
└───────────────────────┼──────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────┐
│                    INCOMING PORTS                                │
│                   (Application API)                              │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  Interface: AddsConversationParticipants                         │
│  Interface: CreatesConversations                                 │
│  Interface: CreatesMessages                                      │
│  Interface: DeletesConversations                                 │
│  Interface: DeletesMessages                                      │
│  Interface: RemovesConversationParticipants                      │
│  Interface: UpdatesConversations                                 │
│                                                                   │
└───────────────────────┬──────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────┐
│                   APPLICATION CORE                               │
│                    (Domain Logic)                                │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  Actions:                                                        │
│  ├── AddParticipant                                             │
│  ├── CreateConversation                                         │
│  ├── CreateMessage                                              │
│  ├── DeleteConversation                                         │
│  ├── DeleteMessage                                              │
│  ├── RemoveParticipant                                          │
│  └── UpdateConversation                                         │
│                                                                   │
│  Repositories:                                                   │
│  ├── ChatRepository                                             │
│  ├── ConversationProvider                                       │
│  └── MessageProvider                                            │
│                                                                   │
│  Models (Domain):                                               │
│  ├── Conversation                                               │
│  ├── Message                                                    │
│  └── Participation                                              │
│                                                                   │
└───────────────────────┬──────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────┐
│                    OUTGOING PORTS                                │
│                 (External Dependencies)                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  Interface: UserSearchContract                                   │
│  Interface: UrnResolverContract                                  │
│  Interface: MediaManagerContract                                 │
│  Interface: AuthorizationContract                                │
│  Interface: BroadcasterContract                                  │
│  Interface: UserDisplayContract                                  │
│                                                                   │
└───────────────────────┬──────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────┐
│                    OUTGOING ADAPTERS                             │
│                   (Driven/Secondary Side)                        │
│              (Implemented in Main Application)                   │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌──────────────────┐  ┌──────────────────┐                     │
│  │ UserSearch       │  │ UrnResolver      │                     │
│  │ Adapter          │  │ Adapter          │                     │
│  │ (App\Services)   │  │ (Packages\Urn)   │                     │
│  └────────┬─────────┘  └────────┬─────────┘                     │
│           │                     │                                │
│           │    ┌────────────────┴─────────┐                     │
│           │    │                          │                     │
│  ┌────────▼────▼────┐          ┌─────────▼────────┐            │
│  │ Media Manager    │          │ Authorization    │            │
│  │ Adapter          │          │ Adapter          │            │
│  │ (MediaLibrary)   │          │ (Laravel Gates)  │            │
│  └──────────────────┘          └──────────────────┘            │
│                                                                   │
│  ┌──────────────────┐          ┌──────────────────┐            │
│  │ Broadcaster      │          │ User Display     │            │
│  │ Adapter          │          │ Adapter          │            │
│  │ (Laravel Events) │          │ (Helpers)        │            │
│  └────────┬─────────┘          └────────┬─────────┘            │
│           │                             │                       │
│           └──────────────┬──────────────┘                       │
│                          │                                      │
└──────────────────────────┼──────────────────────────────────────┘
                           │
                           ▼
                  ┌────────────────┐
                  │   EXTERNAL     │
                  │   SYSTEMS      │
                  │                │
                  │ - Database     │
                  │ - Storage      │
                  │ - Broadcast    │
                  │ - Search       │
                  └────────────────┘
```

## Key Principles

### 1. Dependency Inversion
- **Core depends on interfaces, not implementations**
- Actions inject contracts like `UserSearchContract`, not concrete `User` models
- Main application provides adapters that implement these contracts

### 2. Technology Independence
- The core domain logic has no knowledge of:
  - Which HTTP framework is used (Laravel, Lumen, etc.)
  - How users are stored (database, API, etc.)
  - Broadcasting mechanism (Pusher, Redis, etc.)

### 3. Testability
- Core logic can be tested with mock implementations of external contracts
- No need to boot Laravel or seed database for unit tests
- Integration tests verify adapters work correctly

### 4. Clear Boundaries
- **Incoming Ports**: What Chatly provides (conversation/message management)
- **Outgoing Ports**: What Chatly needs (user search, media storage, etc.)
- **Adapters**: Connect ports to specific technologies

## Flow Example: Creating a Conversation

```
1. HTTP Request
   POST /chatly
   { "participants": ["urn:user:1", "urn:user:2"] }

2. Controller (Incoming Adapter)
   ConversationController@store

3. Incoming Port
   CreatesConversations::create($user, $params)

4. Action (Core)
   CreateConversation->create()
   ├── Validates input
   ├── Uses UrnResolverContract (outgoing port)
   │   └── Resolves URNs to User models
   ├── Uses AuthorizationContract (outgoing port)
   │   └── Checks permissions
   ├── Creates conversation via musonza/chat
   └── Returns Conversation model

5. Response Transformation
   ConversationResource (outgoing adapter for HTTP)

6. HTTP Response
   JSON representation of conversation
```

## Directory Structure

```
modules/chatly/
├── src/
│   ├── Actions/                 # Application Core
│   │   ├── CreateConversation.php
│   │   └── ...
│   │
│   ├── Contracts/               # Ports (Interfaces)
│   │   ├── (Incoming Ports)
│   │   │   ├── CreatesConversations.php
│   │   │   └── ...
│   │   │
│   │   └── External/            # Outgoing Ports
│   │       ├── UserSearchContract.php
│   │       ├── UrnResolverContract.php
│   │       ├── MediaManagerContract.php
│   │       ├── AuthorizationContract.php
│   │       ├── BroadcasterContract.php
│   │       └── UserDisplayContract.php
│   │
│   ├── Http/                    # Incoming Adapters
│   │   ├── Controllers/
│   │   │   ├── Api/            # REST API Adapter
│   │   │   └── Inertia/        # Web UI Adapter
│   │   │
│   │   └── Resource/           # Response Transformers
│   │
│   ├── Models/                  # Domain Models
│   │   ├── Conversation.php
│   │   ├── Message.php
│   │   └── Participation.php
│   │
│   └── Repositories/            # Repository Pattern
│       ├── ChatRepository.php
│       └── Providers/
│
└── docs/
    ├── architecture.md          # This file
    └── external-contracts.md    # Outgoing port details
```

## Benefits

### For Chatly Module
- **Portable**: Can be extracted to standalone package
- **Testable**: Mock external dependencies easily
- **Maintainable**: Clear separation of concerns
- **Flexible**: Swap implementations without changing core

### For Main Application
- **Integration Points Documented**: Contracts serve as API documentation
- **Flexibility**: Can change User model without affecting Chatly
- **Testing**: Can test Chatly integration with stubs
- **Gradual Migration**: Can refactor adapters incrementally

## Anti-Patterns to Avoid

### ❌ Direct Model Imports in Core
```php
// BAD - Core depends on main app
use App\Models\User;

class CreateConversation
{
    public function create($userId) {
        $user = User::find($userId); // Tight coupling
    }
}
```

### ✅ Use Contracts Instead
```php
// GOOD - Core depends on interface
use Modules\Chatly\Contracts\External\UserSearchContract;

class CreateConversation
{
    public function __construct(
        protected UserSearchContract $userSearch
    ) {}
    
    public function create($urn) {
        $user = $this->userSearch->findByUrn($urn); // Loose coupling
    }
}
```

### ❌ Controllers with Business Logic
```php
// BAD - Business logic in adapter
class ConversationController
{
    public function store(Request $request) {
        $user = User::find($request->user_id);
        $conversation = Chat::createConversation(...);
        // validation, authorization, etc mixed with HTTP
    }
}
```

### ✅ Thin Controllers, Fat Actions
```php
// GOOD - Delegate to action
class ConversationController
{
    public function store(
        Request $request,
        CreatesConversations $creator
    ) {
        $conversation = $creator->create(
            $this->currentUser(),
            $request->validated()
        );
        
        return new ConversationResource($conversation);
    }
}
```

## Further Reading

- [Hexagonal Architecture by Alistair Cockburn](https://alistair.cockburn.us/hexagonal-architecture/)
- [Clean Architecture by Robert Martin](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)
- [docs/external-contracts.md](external-contracts.md) - Implementation guide for outgoing ports

