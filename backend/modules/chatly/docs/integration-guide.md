# Chatly Integration Guide

This guide explains how to properly integrate the Chatly module with your main application following the **Ports and Adapters** architecture.

## Problem Statement

Previously, Chatly directly imported main application classes:
```php
// ❌ BAD - Tight coupling
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

This creates several issues:
- **Tight coupling**: Module can't be extracted or reused
- **Testing difficulty**: Can't mock external dependencies
- **Maintenance burden**: Changes to User model affect Chatly
- **Architectural violation**: Module depends on outer layers

## Solution: External Contracts (Outgoing Ports)

Chatly now defines **interfaces** for all external dependencies in `src/Contracts/External/`:

```php
// ✅ GOOD - Loose coupling via interface
use Modules\Chatly\Contracts\External\UserSearchContract;

class SearchController extends InertiaController
{
    public function __construct(
        protected UserSearchContract $userSearch
    ) {}
    
    public function index(Request $request)
    {
        $result = $this->userSearch->search(
            $request->query('keyword')
        );
        // ...
    }
}
```

## Implementation Checklist

### Step 1: Create Adapter Classes

Create adapter implementations in your main application:

```bash
app/Services/Chat/
├── UserSearchAdapter.php
├── UrnResolverAdapter.php
├── MediaManagerAdapter.php
├── AuthorizationAdapter.php
├── BroadcasterAdapter.php
└── UserDisplayAdapter.php
```

**Example: UserSearchAdapter.php**
```php
<?php

namespace App\Services\Chat;

use App\Models\Employee;
use App\Models\User;
use Modules\Chatly\Contracts\External\UserSearchContract;
use Packages\Urn\UrnManager;

class UserSearchAdapter implements UserSearchContract
{
    public function search(string $keyword, array $filters = [], int $limit = 8): array
    {
        $exceptIds = [];
        foreach ($filters['except'] ?? [] as $except) {
            if ($except['type'] == User::class) {
                $exceptIds[] = $except['id'];
            }
        }

        $people = User::search($keyword)
            ->whereNotIn('id', $exceptIds)
            ->limit($limit)
            ->get()
            ->map(function ($person) {
                return [
                    'id' => $person->id,
                    'name' => $person->full_name,
                    'photo' => get_user_avatar_url($person),
                    'type' => User::class,
                    'is_user' => true,
                ];
            });

        $groups = Employee::search($keyword)
            ->get()
            ->groupBy(fn($item) => $item->employer->name)
            ->map(function ($members) {
                return $members->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'name' => $member->user->full_name,
                        'photo' => get_company_avatar_url($member->employer),
                        'type' => Employee::class,
                        'role' => $member->role,
                        'is_user' => false,
                    ];
                });
            });

        return [
            'people' => $people,
            'groups' => $groups,
        ];
    }

    public function findByUrn(string $urn): mixed
    {
        return UrnManager::resolve($urn);
    }

    public function currentUser(): mixed
    {
        return auth()->user();
    }

    public function currentTeam(): mixed
    {
        return session('current_team');
    }
}
```

### Step 2: Register Adapters in Service Provider

Create or update `App\Providers\ChatServiceProvider`:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Chatly\Contracts\External;
use App\Services\Chat;

class ChatServiceProvider extends ServiceProvider
{
    /**
     * Register external contract bindings for Chatly module.
     */
    public function register(): void
    {
        // User search and participant resolution
        $this->app->bind(
            External\UserSearchContract::class,
            Chat\UserSearchAdapter::class
        );

        // URN identifier resolution
        $this->app->bind(
            External\UrnResolverContract::class,
            Chat\UrnResolverAdapter::class
        );

        // Media/file management
        $this->app->bind(
            External\MediaManagerContract::class,
            Chat\MediaManagerAdapter::class
        );

        // Authorization and permissions
        $this->app->bind(
            External\AuthorizationContract::class,
            Chat\AuthorizationAdapter::class
        );

        // Real-time broadcasting
        $this->app->bind(
            External\BroadcasterContract::class,
            Chat\BroadcasterAdapter::class
        );

        // User display information
        $this->app->bind(
            External\UserDisplayContract::class,
            Chat\UserDisplayAdapter::class
        );
    }
}
```

Register this provider in `config/app.php`:
```php
'providers' => [
    // ...
    App\Providers\ChatServiceProvider::class,
],
```

### Step 3: Update Chatly Code to Use Contracts

Refactor existing Chatly code to inject contracts instead of direct dependencies:

**Before:**
```php
use App\Models\User;
use Packages\Urn\UrnManager;

class AddParticipant extends RestApiAction
{
    public function add($user, $joiner, Conversation $conversation)
    {
        if (is_string($joiner)) {
            $joiner = UrnManager::resolve($joiner); // Direct dependency
        }
        // ...
    }
}
```

**After:**
```php
use Modules\Chatly\Contracts\External\UrnResolverContract;

class AddParticipant extends RestApiAction
{
    public function __construct(
        protected UrnResolverContract $urnResolver
    ) {}

    public function add($user, $joiner, Conversation $conversation)
    {
        if (is_string($joiner)) {
            $joiner = $this->urnResolver->resolve($joiner); // Via contract
        }
        // ...
    }
}
```

### Step 4: Update Tests

Update tests to mock contracts instead of concrete implementations:

```php
use Modules\Chatly\Contracts\External\UserSearchContract;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    public function test_search_returns_participants()
    {
        // Mock the external contract
        $mockSearch = $this->mock(UserSearchContract::class);
        $mockSearch->shouldReceive('search')
            ->once()
            ->with('john', [], 8)
            ->andReturn([
                'people' => [
                    [
                        'id' => 1,
                        'name' => 'John Doe',
                        'photo' => 'https://example.com/avatar.jpg',
                        'type' => 'App\\Models\\User',
                        'is_user' => true,
                    ],
                ],
                'groups' => [],
            ]);

        $response = $this->get('/chatly/search?keyword=john');

        $response->assertOk()
            ->assertJson([
                'people' => [
                    ['name' => 'John Doe'],
                ],
            ]);
    }
}
```

## Available Contracts

### 1. UserSearchContract
**Purpose**: Search and resolve chat participants  
**Methods**: `search()`, `findByUrn()`, `currentUser()`, `currentTeam()`  
**Used by**: SearchController, CreateConversation, AddParticipant

### 2. UrnResolverContract
**Purpose**: Convert URN strings to entities and back  
**Methods**: `resolve()`, `generate()`, `isValid()`, `parse()`  
**Used by**: AddParticipant, ParticipantResource

### 3. MediaManagerContract
**Purpose**: Handle file uploads and attachments  
**Methods**: `store()`, `find()`, `getMediaForUser()`, `delete()`, `getAllowedMimeTypes()`  
**Used by**: MessageProvider, AttachmentBuilder

### 4. AuthorizationContract
**Purpose**: Permission checking and authorization  
**Methods**: `can()`, `authorize()`, `hasPermission()`  
**Used by**: All Actions, Controllers

### 5. BroadcasterContract
**Purpose**: Real-time event broadcasting  
**Methods**: `broadcast()`, `broadcastToConversation()`, `broadcastToUser()`, `isEnabled()`  
**Used by**: ChatBroadcaster, MessageProvider

### 6. UserDisplayContract
**Purpose**: Avatar and display name resolution  
**Methods**: `getAvatarUrl()`, `getCompanyAvatarUrl()`, `getDisplayName()`, `isTeamMember()`  
**Used by**: ParticipantResource, ConversationProvider

## Migration Strategy

### Phase 1: Create Adapters (No Breaking Changes)
1. Create adapter classes in `app/Services/Chat/`
2. Register them in service provider
3. Test adapters work correctly

### Phase 2: Refactor Chatly Controllers
1. Update SearchController to use UserSearchContract
2. Update other controllers one by one
3. Run tests to verify functionality

### Phase 3: Refactor Chatly Actions
1. Update Actions to inject contracts
2. Remove direct imports of App\Models
3. Update tests to mock contracts

### Phase 4: Refactor Resources & Repositories
1. Update ParticipantResource
2. Update Repository providers
3. Final testing and cleanup

## Benefits

✅ **Decoupling**: Chatly doesn't depend on main app structure  
✅ **Testability**: Easy to mock external dependencies  
✅ **Portability**: Can extract Chatly to separate package  
✅ **Flexibility**: Can change User model without affecting Chatly  
✅ **Documentation**: Contracts serve as integration API docs  
✅ **Maintainability**: Clear boundaries and responsibilities  

## Troubleshooting

### Container Resolution Errors
```
Error: Target class [Modules\Chatly\Contracts\External\UserSearchContract] does not exist.
```

**Solution**: Make sure you registered ChatServiceProvider in `config/app.php`.

### Type Errors in Adapters
```
Error: Argument #1 must be of type User, Employee given
```

**Solution**: Use `mixed` type hints in contracts to support polymorphism (User or Employee).

### Tests Failing After Refactoring
**Solution**: Update tests to mock the new contracts instead of old dependencies.

## Further Reading

- [docs/architecture.md](docs/architecture.md) - Complete architecture overview
- [docs/external-contracts.md](docs/external-contracts.md) - Detailed contract specifications
- [agents.md](agents.md) - Guidelines for using contracts in agents

## Support

For questions or issues:
1. Review the architecture documentation
2. Check example adapter implementations
3. Verify service provider bindings
4. Run `php artisan config:clear` and `php artisan cache:clear`

