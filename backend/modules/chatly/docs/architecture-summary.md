# Chatly Architecture Summary

## Question Addressed

**Where should I define the repository interface for user searching?**  
**Define the interfaces/domain for external systems that chatly uses.**

## Answer: External Contracts (Outgoing Ports)

All external system interfaces are defined in:
```
modules/chatly/src/Contracts/External/
```

This follows the **Ports and Adapters (Hexagonal Architecture)** pattern where:
- **Outgoing Ports** = Interfaces the module needs from external systems
- **Adapters** = Implementations provided by the main application

## Created Files

### 1. External Contract Interfaces

#### `src/Contracts/External/UserSearchContract.php`
**Purpose**: Search users and team members, resolve participants  
**Methods**:
- `search(string $keyword, array $filters, int $limit): array`
- `findByUrn(string $urn): mixed`
- `currentUser(): mixed`
- `currentTeam(): mixed`

**Replaces**: Direct imports of `App\Models\User` and `App\Models\Employee`

---

#### `src/Contracts/External/UrnResolverContract.php`
**Purpose**: URN string resolution and generation  
**Methods**:
- `resolve(string $urn): mixed`
- `generate(mixed $entity): string`
- `isValid(string $urn): bool`
- `parse(string $urn): array`

**Replaces**: Direct usage of `Packages\Urn\UrnManager`

---

#### `src/Contracts/External/MediaManagerContract.php`
**Purpose**: File/media management for attachments  
**Methods**:
- `store(UploadedFile $file, string $collection, mixed $owner): array`
- `find(int $mediaId): ?array`
- `getMediaForUser(mixed $user, array $filters): array`
- `delete(int $mediaId): bool`
- `getAllowedMimeTypes(): array`

**Replaces**: Direct usage of `Packages\MediaLibrary`

---

#### `src/Contracts/External/AuthorizationContract.php`
**Purpose**: Permission checking and authorization  
**Methods**:
- `can(mixed $user, string $ability, mixed $resource): bool`
- `authorize(mixed $user, string $ability, mixed $resource): void`
- `hasPermission(mixed $user, string $permission): bool`

**Replaces**: Direct usage of `Gate::forUser()` and permission checks

---

#### `src/Contracts/External/BroadcasterContract.php`
**Purpose**: Real-time event broadcasting  
**Methods**:
- `broadcast(string $event, array $data, array $channels): void`
- `broadcastToConversation(mixed $conversation, string $event, array $data): void`
- `broadcastToUser(mixed $user, string $event, array $data): void`
- `isEnabled(): bool`

**Replaces**: Direct usage of Laravel's `broadcast()` function

---

#### `src/Contracts/External/UserDisplayContract.php`
**Purpose**: User avatars and display information  
**Methods**:
- `getAvatarUrl(mixed $user): string`
- `getCompanyAvatarUrl(mixed $company): string`
- `getDisplayName(mixed $user): string`
- `isTeamMember(mixed $entity): bool`

**Replaces**: Direct usage of `get_user_avatar_url()` and `get_company_avatar_url()` helpers

---

### 2. Documentation Files

#### `docs/external-contracts.md`
Complete guide for implementing adapters:
- Contract specifications
- Example adapter implementations
- Service provider bindings
- Testing examples
- Migration strategy

#### `docs/architecture.md`
Hexagonal architecture overview:
- Visual diagrams of ports and adapters
- Flow examples
- Anti-patterns to avoid
- Directory structure
- Architectural principles

#### `INTEGRATION_GUIDE.md`
Step-by-step integration guide:
- Problem statement
- Solution explanation
- Implementation checklist
- Code examples
- Troubleshooting

#### Updated Files:
- `agents.md` - Added external contracts section
- `README.md` - Added link to external-contracts.md
- `docs/module-guide.md` - Reorganized with architecture section

---

## Architecture Pattern

```
┌─────────────────────────┐
│   HTTP Controllers      │  ← Incoming Adapters
│   (API/Inertia)         │
└───────────┬─────────────┘
            │
            ▼
┌─────────────────────────┐
│   Action Interfaces     │  ← Incoming Ports
│   (CreatesConversations)│
└───────────┬─────────────┘
            │
            ▼
┌─────────────────────────┐
│   Actions & Domain      │  ← Application Core
│   (CreateConversation)  │
└───────────┬─────────────┘
            │
            ▼
┌─────────────────────────┐
│   External Contracts    │  ← Outgoing Ports (NEW!)
│   (UserSearchContract)  │
└───────────┬─────────────┘
            │
            ▼
┌─────────────────────────┐
│   Adapter Implementations│ ← Outgoing Adapters
│   (in main app)         │     (To be created)
└─────────────────────────┘
```

## Next Steps for Integration

1. **Create Adapters** in main application:
   ```
   app/Services/Chat/
   ├── UserSearchAdapter.php
   ├── UrnResolverAdapter.php
   ├── MediaManagerAdapter.php
   ├── AuthorizationAdapter.php
   ├── BroadcasterAdapter.php
   └── UserDisplayAdapter.php
   ```

2. **Register Bindings** in service provider:
   ```php
   $this->app->bind(
       \Modules\Chatly\Contracts\External\UserSearchContract::class,
       \App\Services\Chat\UserSearchAdapter::class
   );
   ```

3. **Refactor Chatly** to inject contracts:
   ```php
   // Before
   use App\Models\User;
   $users = User::search($keyword)->get();
   
   // After
   use Modules\Chatly\Contracts\External\UserSearchContract;
   public function __construct(protected UserSearchContract $userSearch) {}
   $result = $this->userSearch->search($keyword);
   ```

4. **Update Tests** to mock contracts

## Benefits Achieved

✅ **Decoupling**: Chatly no longer imports `App\Models\*`  
✅ **Clear Boundaries**: Explicit interfaces define dependencies  
✅ **Testability**: Can mock all external dependencies  
✅ **Portability**: Module can be extracted to package  
✅ **Documentation**: Contracts serve as integration API  
✅ **Flexibility**: Main app can change without affecting Chatly  

## Summary

The **outgoing ports** (external contracts) define what Chatly **needs** from external systems, while **incoming ports** (action interfaces) define what Chatly **provides**. This creates a clean, testable, and maintainable architecture following industry best practices.

All interfaces are properly documented with examples, making integration straightforward for the main application team.

