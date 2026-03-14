# External System Contracts (Outgoing Ports)

This document describes the **outgoing ports** (external dependencies) that Chatly module requires from the host application.

## Overview

Following the **Ports and Adapters (Hexagonal Architecture)** pattern, Chatly defines interfaces for all external systems it depends on. The main application must provide **adapters** that implement these contracts.

## Required Contracts

All contracts are located in `src/Contracts/External/`.

### 1. UserSearchContract

**Purpose**: Search for users and team members who can participate in conversations.

**Location**: `Modules\Chatly\Contracts\External\UserSearchContract`

**Methods**:
- `search(string $keyword, array $filters, int $limit): array`
- `findByUrn(string $urn): mixed`
- `currentUser(): mixed`
- `currentTeam(): mixed`

**Adapter Implementation**: Should be bound in `App\Providers\ApiServiceProvider` or similar:

```php
$this->app->bind(
    \Modules\Chatly\Contracts\External\UserSearchContract::class,
    \App\Services\Chat\UserSearchAdapter::class
);
```

**Example Adapter**:
```php
namespace App\Services\Chat;

use App\Models\Employee;
use App\Models\User;
use Modules\Chatly\Contracts\External\UserSearchContract;

class UserSearchAdapter implements UserSearchContract
{
    public function search(string $keyword, array $filters = [], int $limit = 8): array
    {
        // Implementation using User::search() and Employee::search()
        $people = User::search($keyword)->limit($limit)->get();
        $groups = Employee::search($keyword)->get()->groupBy(...);
        
        return [
            'people' => $people->map(...),
            'groups' => $groups->map(...),
        ];
    }
    
    // ... other methods
}
```

---

### 2. UrnResolverContract

**Purpose**: Convert URN strings to entities and vice versa.

**Location**: `Modules\Chatly\Contracts\External\UrnResolverContract`

**Methods**:
- `resolve(string $urn): mixed`
- `generate(mixed $entity): string`
- `isValid(string $urn): bool`
- `parse(string $urn): array`

**Current Implementation**: `Packages\Urn\UrnManager`

**Adapter Example**:
```php
namespace App\Services\Chat;

use Modules\Chatly\Contracts\External\UrnResolverContract;
use Packages\Urn\UrnManager as UrnService;

class UrnResolverAdapter implements UrnResolverContract
{
    public function resolve(string $urn): mixed
    {
        return UrnService::resolve($urn);
    }
    
    public function generate(mixed $entity): string
    {
        return UrnService::generate($entity);
    }
    
    // ... other methods
}
```

---

### 3. MediaManagerContract

**Purpose**: Handle file uploads, storage, and retrieval for chat attachments.

**Location**: `Modules\Chatly\Contracts\External\MediaManagerContract`

**Methods**:
- `store(UploadedFile $file, string $collection, mixed $owner): array`
- `find(int $mediaId): ?array`
- `getMediaForUser(mixed $user, array $filters): array`
- `delete(int $mediaId): bool`
- `getAllowedMimeTypes(): array`

**Current Implementation**: `Packages\MediaLibrary`

---

### 4. AuthorizationContract

**Purpose**: Check permissions and authorize actions.

**Location**: `Modules\Chatly\Contracts\External\AuthorizationContract`

**Methods**:
- `can(mixed $user, string $ability, mixed $resource): bool`
- `authorize(mixed $user, string $ability, mixed $resource): void`
- `hasPermission(mixed $user, string $permission): bool`

**Adapter Example**:
```php
namespace App\Services\Chat;

use Illuminate\Support\Facades\Gate;
use Modules\Chatly\Contracts\External\AuthorizationContract;

class AuthorizationAdapter implements AuthorizationContract
{
    public function authorize(mixed $user, string $ability, mixed $resource = null): void
    {
        Gate::forUser($user)->authorize($ability, $resource);
    }
    
    public function can(mixed $user, string $ability, mixed $resource = null): bool
    {
        return Gate::forUser($user)->allows($ability, $resource);
    }
    
    public function hasPermission(mixed $user, string $permission): bool
    {
        return $user->hasPermissionTo($permission);
    }
}
```

---

### 5. BroadcasterContract

**Purpose**: Send real-time events to connected clients.

**Location**: `Modules\Chatly\Contracts\External\BroadcasterContract`

**Methods**:
- `broadcast(string $event, array $data, array $channels): void`
- `broadcastToConversation(mixed $conversation, string $event, array $data): void`
- `broadcastToUser(mixed $user, string $event, array $data): void`
- `isEnabled(): bool`

**Current Implementation**: Wrapped by `Modules\Chatly\Repositories\Services\ChatBroadcaster`

---

### 6. UserDisplayContract

**Purpose**: Get display information (avatars, names) for users and organizations.

**Location**: `Modules\Chatly\Contracts\External\UserDisplayContract`

**Methods**:
- `getAvatarUrl(mixed $user): string`
- `getCompanyAvatarUrl(mixed $company): string`
- `getDisplayName(mixed $user): string`
- `isTeamMember(mixed $entity): bool`

**Adapter Example**:
```php
namespace App\Services\Chat;

use Modules\Chatly\Contracts\External\UserDisplayContract;

class UserDisplayAdapter implements UserDisplayContract
{
    public function getAvatarUrl(mixed $user): string
    {
        return get_user_avatar_url($user);
    }
    
    public function getCompanyAvatarUrl(mixed $company): string
    {
        return get_company_avatar_url($company);
    }
    
    // ... other methods
}
```

---

## Binding Contracts in Service Provider

All contracts should be bound in a service provider (e.g., `App\Providers\ChatServiceProvider`):

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ChatServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind external contracts to their adapters
        $this->app->bind(
            \Modules\Chatly\Contracts\External\UserSearchContract::class,
            \App\Services\Chat\UserSearchAdapter::class
        );
        
        $this->app->bind(
            \Modules\Chatly\Contracts\External\UrnResolverContract::class,
            \App\Services\Chat\UrnResolverAdapter::class
        );
        
        $this->app->bind(
            \Modules\Chatly\Contracts\External\MediaManagerContract::class,
            \App\Services\Chat\MediaManagerAdapter::class
        );
        
        $this->app->bind(
            \Modules\Chatly\Contracts\External\AuthorizationContract::class,
            \App\Services\Chat\AuthorizationAdapter::class
        );
        
        $this->app->bind(
            \Modules\Chatly\Contracts\External\BroadcasterContract::class,
            \App\Services\Chat\BroadcasterAdapter::class
        );
        
        $this->app->bind(
            \Modules\Chatly\Contracts\External\UserDisplayContract::class,
            \App\Services\Chat\UserDisplayAdapter::class
        );
    }
}
```

---

## Benefits of This Architecture

1. **Decoupling**: Chatly module doesn't directly depend on main app models or services
2. **Testability**: Easy to mock external contracts in tests
3. **Flexibility**: Can swap implementations without changing Chatly code
4. **Portability**: Chatly can be extracted to a separate package
5. **Clear Boundaries**: Explicit definition of what Chatly needs from outside
6. **Documentation**: Contracts serve as documentation for integration points

---

## Migration Path

To refactor existing code to use these contracts:

1. **Create adapter classes** in `app/Services/Chat/` that implement the contracts
2. **Bind contracts** in a service provider
3. **Update Chatly code** to inject and use contracts instead of direct dependencies
4. **Update tests** to mock contracts instead of concrete implementations

Example refactoring for `SearchController`:

**Before**:
```php
use App\Models\User;
use App\Models\Employee;

class SearchController extends InertiaController
{
    public function index(Request $request)
    {
        $people = User::search($keyword)->get();
        $groups = Employee::search($keyword)->get();
        // ...
    }
}
```

**After**:
```php
use Modules\Chatly\Contracts\External\UserSearchContract;

class SearchController extends InertiaController
{
    public function __construct(
        protected UserSearchContract $userSearch
    ) {}
    
    public function index(Request $request)
    {
        $keyword = $request->query('keyword');
        $result = $this->userSearch->search($keyword);
        // ...
    }
}
```

---

## Testing Example

```php
use Modules\Chatly\Contracts\External\UserSearchContract;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    public function test_search_returns_users_and_groups()
    {
        // Mock the contract
        $mockSearch = $this->mock(UserSearchContract::class);
        $mockSearch->shouldReceive('search')
            ->with('john', [], 8)
            ->andReturn([
                'people' => [...],
                'groups' => [...],
            ]);
        
        $response = $this->get('/chatly/search?keyword=john');
        
        $response->assertOk();
    }
}
```

