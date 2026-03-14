# Tenancy Package

A Laravel package that provides team/tenancy context management for multi-tenant applications. This package allows users to switch between teams and maintains the current team context across different authentication guards (web sessions and API tokens).

## Overview

The Tenancy package provides a flexible abstraction layer for storing and retrieving the current team ID for authenticated users. It supports multiple storage backends based on the authentication guard being used:

- **Web Guard**: Stores team ID in session storage
- **Sanctum Guard**: Stores team ID in the user model's `current_team_id` field
- **Testing**: Uses in-memory storage for test isolation

### Real-World Usage

This package is used throughout the Sowidu application via the `HasTeams` trait:

- **User Model**: `app/Models/User.php` uses `App\Models\Concerns\HasTeams`
- **HasTeams Trait**: `app/Models/Concerns/HasTeams.php` integrates with `TeamStoreFactory`
- The trait provides methods like `currentTeam()`, `switchTeam()`, and `isCurrentTeam()` for easy team management

## Installation

The package is included in the Sowidu monorepo. If you need to install it separately, add it to your `composer.json`:

```json
{
    "require": {
        "sowidu/tenancy": "1.x-dev"
    }
}
```

The service provider is auto-discovered via Laravel's package discovery. If you need to register it manually:

```php
// config/app.php
'providers' => [
    // ...
    Packages\Tenancy\TenancyServiceProvider::class,
],
```

## Architecture

The package follows a factory pattern with a simple interface:

```
TeamStore (Interface)
    ├── WebGuardStore (Session-based storage)
    ├── SanctumGuardStore (Database-based storage)
    └── TestGuardStore (In-memory storage for tests)

TeamStoreFactory (Creates appropriate store based on guard)
```

### Components

#### TeamStore Interface

Defines the contract for all team storage implementations:

```php
interface TeamStore
{
    public function setTeamId(Model $user, $teamId): void;
    public function getTeamId(Model $user): ?int;
}
```

#### WebGuardStore

Stores the team ID in the session. Uses a hashed session key based on the user ID to prevent conflicts between users.

**Storage**: Laravel session  
**Key Format**: `md5('teamstore-session-{user_id}')`

#### SanctumGuardStore

Stores the team ID directly in the user model's `current_team_id` field. This persists across API requests.

**Storage**: User model database column (`current_team_id`)  
**Requirement**: User model must have a `current_team_id` column

#### TestGuardStore

In-memory storage using a static array. Automatically used when running tests.

**Storage**: Static array in memory  
**Isolation**: Can be cleared between tests using `clear()` or `clearFor()` methods

#### TeamStoreFactory

Factory class that creates the appropriate `TeamStore` implementation based on:

1. **Testing Environment**: Automatically uses `TestGuardStore` when `APP_ENV=testing` or when running unit tests
2. **Guard Type**: Uses `WebGuardStore` for `web` guard, `SanctumGuardStore` for `sanctum` guard
3. **Default Guard**: Falls back to `config('auth.defaults.guard')` if no guard is specified

## Usage

### Basic Usage

The package is typically used through the `TeamStoreFactory`:

```php
use Packages\Tenancy\TeamStoreFactory;

// Get the current team ID for a user
$teamStore = TeamStoreFactory::create();
$teamId = $teamStore->getTeamId($user);

// Set the current team ID for a user
$teamStore->setTeamId($user, $teamId);

// Specify a guard explicitly
$teamStore = TeamStoreFactory::create('sanctum');
$teamStore->setTeamId($user, $teamId);
```

### Integration with HasTeams Trait

The package is designed to work with a `HasTeams` trait. The trait is used in the `User` model for team management. Here's the actual implementation from the codebase:

```php
use Packages\Tenancy\TeamStoreFactory;

trait HasTeams
{
    public function currentTeam()
    {
        $teamId = TeamStoreFactory::create()->getTeamId($this);
        
        return $this->teams()->find($teamId);
    }
    
    public function switchTeam($team)
    {
        if (!is_null($team) && !$this->belongsToTeam($team)) {
            return false;
        }
        
        TeamStoreFactory::create()->setTeamId($this, $team?->id);
        
        return true;
    }
    
    public function isCurrentTeam($team)
    {
        return $this->currentTeam()?->id === $team->id;
    }
}
```

**Usage in User Model:**

```php
use App\Models\Concerns\HasTeams;

class User extends Organization
{
    use HasTeams;
    
    // User can now switch teams
    public function someMethod()
    {
        $user = auth()->user();
        
        // Get current team
        $currentTeam = $user->currentTeam();
        
        // Switch to a different team
        $user->switchTeam($newTeam);
        
        // Check if a team is the current team
        $isCurrent = $user->isCurrentTeam($team);
    }
}
```

### User Model Setup

For Sanctum guard to work, ensure your User model has a `current_team_id` column:

```php
// Migration
Schema::table('users', function (Blueprint $table) {
    $table->unsignedBigInteger('current_team_id')->nullable()->after('id');
    $table->index('current_team_id');
});
```

### Controller Usage

Example controller method for switching teams:

```php
public function switchTeam(Request $request, Company $team)
{
    $user = $request->user();
    
    if (!$user->belongsToTeam($team)) {
        abort(403, 'You do not belong to this team.');
    }
    
    $user->switchTeam($team);
    
    return redirect()->back()->with('success', 'Team switched successfully.');
}
```

### API Usage (Sanctum)

When using Sanctum for API authentication, the team ID is stored in the user model:

```php
// API endpoint
Route::middleware('auth:sanctum')->post('/switch-team', function (Request $request) {
    $user = $request->user();
    $teamId = $request->input('team_id');
    
    $team = $user->teams()->find($teamId);
    
    if (!$team) {
        return response()->json(['error' => 'Team not found'], 404);
    }
    
    $user->switchTeam($team);
    
    return response()->json([
        'message' => 'Team switched successfully',
        'team' => $team
    ]);
});
```

## Testing

The package includes testing utilities to simplify team context management in tests.

### WithTeamContext Trait

Use the `WithTeamContext` trait in your test classes:

```php
use Packages\Tenancy\Testing\WithTeamContext;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;

class MyFeatureTest extends TestCase
{
    use WithTeamContext;
    
    public function test_user_can_access_team_resources()
    {
        // Create user with company and authenticate
        ['user' => $user, 'company' => $company] = $this->setUpUserWithCompany();
        
        // The user is now authenticated and has the company set as current team
        $this->assertEquals($company->id, $user->currentTeam()->id);
    }
    
    public function test_user_with_specific_team()
    {
        $user = User::factory()->create();
        $team = Company::factory()->forUser($user)->create();
        
        // Authenticate with specific team
        $this->actingAsWithTeam($user, $team);
        
        // Assertions...
    }
}
```

### Manual Test Store Management

You can also manually manage the test store:

```php
use Packages\Tenancy\TestGuardStore;

class MyTest extends TestCase
{
    protected function tearDown(): void
    {
        TestGuardStore::clear(); // Clear all team IDs
        parent::tearDown();
    }
    
    public function test_specific_user()
    {
        $user = User::factory()->create();
        $team = Company::factory()->create();
        
        TestGuardStore::clearFor($user); // Clear for specific user
        // ... your test
    }
}
```

### Testing Helper Methods

The `WithTeamContext` trait provides:

#### `actingAsWithTeam(User $user, ?Company $team = null)`

Authenticates a user and sets their current team in one call. Automatically attaches the user to the team if needed.

```php
$user = User::factory()->create();
$team = Company::factory()->create();

$this->actingAsWithTeam($user, $team);
```

#### `setUpUserWithCompany(?User $user = null)`

Creates a user with an associated company and sets it as the current team. Returns an array with both `user` and `company`.

```php
['user' => $user, 'company' => $company] = $this->setUpUserWithCompany();
```

## API Reference

### TeamStoreFactory

#### `create(?string $guard = null): TeamStore`

Creates a `TeamStore` instance for the given guard. Automatically detects testing environment.

**Parameters:**
- `$guard` (string|null): The authentication guard name. Defaults to `config('auth.defaults.guard')`.

**Returns:** `TeamStore` instance

**Example:**
```php
$store = TeamStoreFactory::create('web');
$store = TeamStoreFactory::create('sanctum');
$store = TeamStoreFactory::create(); // Uses default guard
```

### TeamStore Interface

#### `setTeamId(Model $user, $teamId): void`

Sets the current team ID for the given user.

**Parameters:**
- `$user` (Model): The user model instance
- `$teamId` (int|null): The team ID to set, or `null` to clear

**Example:**
```php
$store->setTeamId($user, 123);
$store->setTeamId($user, null); // Clear team
```

#### `getTeamId(Model $user): ?int`

Retrieves the current team ID for the given user.

**Parameters:**
- `$user` (Model): The user model instance

**Returns:** `int|null` The team ID or `null` if not set

**Example:**
```php
$teamId = $store->getTeamId($user);
```

### TestGuardStore

#### `clear(): void`

Clears all stored team IDs. Useful for resetting state between tests.

**Example:**
```php
TestGuardStore::clear();
```

#### `clearFor($user): void`

Clears the team ID for a specific user.

**Parameters:**
- `$user` (Model|int): The user model instance or user ID

**Example:**
```php
TestGuardStore::clearFor($user);
TestGuardStore::clearFor(123);
```

## Configuration

The package uses Laravel's authentication configuration. Ensure your `config/auth.php` is properly configured:

```php
'defaults' => [
    'guard' => 'web', // or 'sanctum' for API
],

'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    
    'sanctum' => [
        'driver' => 'sanctum',
        'provider' => 'users',
    ],
],
```

## Database Requirements

### For Sanctum Guard

Your users table must include a `current_team_id` column:

```php
Schema::table('users', function (Blueprint $table) {
    $table->unsignedBigInteger('current_team_id')->nullable();
});
```

### For Web Guard

No database changes required. Uses Laravel's session storage.

## Security Considerations

1. **Team Validation**: Always validate that a user belongs to a team before allowing them to switch to it.

2. **Authorization**: The package does not perform authorization checks. Always verify team membership in your application logic.

3. **Session Security**: For web guard, the session key is hashed using the user ID, preventing direct session manipulation.

4. **API Security**: For Sanctum guard, ensure your API endpoints properly validate team context.

## Common Patterns

### Middleware for Team Context

```php
class EnsureTeamContext
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        
        if ($user && !$user->currentTeam()) {
            // Redirect to team selection or assign default team
            $firstTeam = $user->teams()->first();
            if ($firstTeam) {
                $user->switchTeam($firstTeam);
            }
        }
        
        return $next($request);
    }
}
```

### Scope Queries by Current Team

```php
class Model extends Eloquent
{
    public function scopeForCurrentTeam($query)
    {
        $user = auth()->user();
        $teamId = TeamStoreFactory::create()->getTeamId($user);
        
        return $query->where('company_id', $teamId);
    }
}
```

## Troubleshooting

### Team ID Not Persisting

- **Web Guard**: Ensure sessions are properly configured and working
- **Sanctum Guard**: Verify the `current_team_id` column exists and is fillable in your User model

### Test Failures

- Use `TestGuardStore::clear()` in `tearDown()` to prevent test interference
- Ensure you're using the `WithTeamContext` trait for team-aware tests

### Guard Not Supported

If you see "Unsupported team store for guard [x]":

1. Check that the guard name is correct
2. Add a new `TeamStore` implementation for your custom guard
3. Register it in `TeamStoreFactory::create()`

## Contributing

When contributing to this package:

1. Follow the existing code style and patterns
2. Add tests for new features
3. Update this documentation
4. Ensure compatibility with both web and Sanctum guards

## License

This package is part of the Sowidu monorepo and follows the project's license.

