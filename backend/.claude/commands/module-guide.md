# Module Guide

Show the modularization patterns, conventions, and architecture used in this project.

## Instructions

When this command is invoked, provide a comprehensive guide on:

1. **Module Structure** - Explain the standard module folder structure following `modules/invoicify` as reference
2. **External Contracts Pattern** - Explain the Ports & Adapters pattern for module isolation
3. **Action Class Standards** - Show the required patterns for action classes
4. **File Naming Conventions** - List naming rules for PHP, Vue, docs, configs
5. **Current Modules** - List existing modules and their status

## Reference Pattern (Invoicify)

```
modules/{module-name}/
├── composer.json              # PSR-4: Modules\{ModuleName}\ => src/
├── config/{module}.php        # Route prefix, middleware config
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── resources/views/
├── routes/web.php
├── src/
│   ├── {Module}ServiceProvider.php
│   ├── Actions/               # Business logic
│   ├── Contracts/
│   │   ├── Actions/           # Action interfaces
│   │   └── External/          # External dependency interfaces (CRITICAL)
│   ├── Data/                  # DTOs
│   ├── Enums/
│   ├── Events/
│   ├── Http/Controllers/
│   ├── Models/
│   ├── Policies/
│   ├── Services/
│   └── Transformers/
└── tests/
```

## Critical Rule: External Contracts

Modules must NEVER import from `App\` namespace directly. Always use contracts:

```php
// BAD - Direct import
use App\Models\User;

// GOOD - Use contract
use Modules\Chatly\Contracts\External\UserSearchContract;
```

## Action Class Pattern

```php
namespace Modules\{Module}\Actions;

use Modules\{Module}\Contracts\Actions\{ActionName}Contract;

class {ActionName}Action implements {ActionName}Contract
{
    public function handle($user, $data, $teamId = null, $errorBag = null)
    {
        // Authorization
        // Validation
        // Business logic
        // Return data (NOT HTTP response)
    }
}
```

## Existing Modules Status

| Module | Status | Notes |
|--------|--------|-------|
| invoicify | Reference | Most complete - use as template |
| offer | Complete | Fully self-contained |
| catalog | Partial | Has external contracts |
| chatly | Partial | Has external contracts |
| todos | Partial | Multiple providers pattern |
| worklogs | Partial | Multiple providers pattern |
| company | Minimal | Only models |
| deliveryticket | Early | Needs structure |
