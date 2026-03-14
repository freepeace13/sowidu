---
name: module-base
description: Base agent with shared rules for all Sowidu modules. Use as reference for module patterns and conventions.
tools: Read, Grep, Glob
model: sonnet
---

# Base Module Agent Rules

All module agents inherit these rules. Module-specific agents extend this with domain knowledge.

## Agent Self-Update Protocol

When completing tasks, agents MUST update TWO files:

### 1. Agent File (`.claude/agents/{module}.md`)
Update progress tracker after completing tasks:
- After adding external contracts → Update contracts count
- After adding tests → Update test count/coverage
- After creating Vite config → Update Vite status
- After migrating frontend → Update frontend status

### 2. Module CLAUDE.md (`modules/{module}/CLAUDE.md`)
Update the auto-loaded module context:
- Update completed tasks list
- Update any changed patterns or conventions
- Keep "Current Focus" section current

**Agent file format:**
```markdown
## Progress Tracker

| Task | Status | Notes |
|------|--------|-------|
| External contracts | 3/22 | Added UserContract, CompanyContract, OrderContract |
| Tests | 15% | Unit tests for actions |
| Vite config | Complete | Created {module}.vite.mjs |
| Frontend migration | Partial | 3/5 pages moved |

**Last updated**: 2024-01-15 - Added 3 contracts
```

**Module CLAUDE.md format:**
```markdown
# {Module} Module

> Auto-loaded when working in this module

## Completed
- [x] External contracts defined
- [ ] All adapters implemented

## Current Focus
Working on: Adding tests for CreateInvoiceAction

## Quick Reference
- Entry point: src/{Module}ServiceProvider.php
- External contracts: src/Contracts/External/
```

Agents should update both files using the Edit tool after completing tasks.

## Critical Rules

### Module Isolation (NEVER violate)

Modules must NEVER directly import from `App\` namespace:

```php
// BAD - Direct import (FORBIDDEN)
use App\Models\User;
use App\Services\MediaService;

// GOOD - Use external contracts
use Modules\{Module}\Contracts\External\UserContract;
use Modules\{Module}\Contracts\External\MediaManagerContract;
```

### Cross-Module Dependencies

When module A needs module B's functionality:

1. Module B defines contract in `src/Contracts/Exposed/`
2. Module A imports that contract
3. Binding registered in main app's service provider

```php
// Module A using Module B's exposed contract
use Modules\Invoicify\Contracts\Exposed\GeneratesInvoiceContract;
```

### Type Safety

All PHP files MUST have:

```php
<?php

declare(strict_types=1);
```

- All method parameters MUST have type hints
- All methods MUST have return types
- Use `?string` or `string|null` for nullable

## Action Class Standards

Actions must follow this pattern:

```php
<?php

declare(strict_types=1);

namespace Modules\{Module}\Actions;

use Modules\{Module}\Contracts\Actions\{ActionName}Contract;

class {ActionName}Action implements {ActionName}Contract
{
    public function __construct(
        // Inject dependencies via constructor
    ) {}

    public function execute(
        $user,
        $data,
        ?int $teamId = null,
        ?string $errorBag = null
    ): mixed {
        // 1. Authorization
        // 2. Validation
        // 3. Business logic
        // 4. Return data (NEVER HTTP response)
    }
}
```

**Rules:**
- MUST implement interface from `Contracts/Actions/`
- MUST accept `$teamId` parameter (nullable)
- MUST accept `$errorBag` parameter (nullable)
- MUST return data/models, NOT HTTP responses

## File Naming Conventions

| Type | Convention | Example |
|------|------------|---------|
| Actions | `{Verb}{Noun}Action.php` | `CreateInvoiceAction.php` |
| Contracts | `{Verb}{Noun}Contract.php` | `CreatesInvoiceContract.php` |
| Adapters | `{Name}Adapter.php` | `UserSearchAdapter.php` |
| DTOs | `{Name}Data.php` | `InvoiceData.php` |
| Vue Components | PascalCase.vue | `InvoiceList.vue` |
| Tests | `{ClassName}Test.php` | `CreateInvoiceActionTest.php` |

## Module Structure

```
modules/{module}/
├── src/
│   ├── {Module}ServiceProvider.php
│   ├── Actions/
│   ├── Contracts/
│   │   ├── Actions/           # Action interfaces
│   │   ├── External/          # Dependencies on App\
│   │   └── Exposed/           # Contracts for other modules
│   ├── Data/                  # DTOs
│   ├── Http/Controllers/
│   ├── Models/
│   └── Policies/
├── resources/
│   ├── css/                   # Module styles
│   └── js/
│       ├── main.js            # Entry point
│       ├── Pages/             # Inertia pages
│       ├── Components/        # Vue components
│       ├── Composables/       # Vue composables
│       ├── Mixins/            # Vue mixins (legacy)
│       ├── Partials/          # Reusable partials
│       ├── Services/          # JS services
│       ├── Enums/             # JS enums
│       └── types/             # TypeScript types
├── routes/web.php
└── tests/
```

## Frontend Standards

### Vite Configuration

Each module with frontend assets needs a Vite config.

**Step 1: Create module config** (`modules/{module}/{module}.vite.mjs`):

```javascript
import path from 'path'
import { fileURLToPath } from 'url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

export default {
    input: [
        'modules/{module}/resources/js/core.js',
        'modules/{module}/resources/css/styles.css',
    ],
    alias: {
        '@{Module}': path.resolve(__dirname, './resources/js'),
    },
}
```

**Step 2: Register in main `vite.config.mjs`**:

```javascript
// Import
import {module}Config from './modules/{module}/{module}.vite.mjs'

// Add alias
'~{Module}': path.resolve(__dirname, './modules/{module}/resources/js'),
...({module}Config?.alias || {}),

// Add inputs
...({module}Config?.input || []),
```

### Vue/Inertia Patterns

**Page component structure:**
```vue
<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import ComponentName from '@ModuleName/Components/ComponentName.vue'

const props = defineProps({
    data: Object,
})
</script>

<template>
    <ModuleLayout>
        <!-- Page content -->
    </ModuleLayout>
</template>
```

**Import aliases:**
```javascript
// From module's own resources
import Component from '@ModuleName/Components/Component.vue'
import { useHelper } from '~ModuleName/Composables/useHelper'

// From shared resources
import SharedComponent from '@/Components/SharedComponent.vue'
```

### Frontend File Naming

| Type | Convention | Example |
|------|------------|---------|
| Pages | PascalCase.vue | `Index.vue`, `Show.vue` |
| Components | PascalCase.vue | `InvoiceList.vue` |
| Composables | camelCase.js | `useInvoice.js` |
| Services | PascalCase.js | `InvoiceService.js` |
| Mixins | camelCase.js | `invoiceMixin.js` |

### Inertia Page Routing

```php
// In controller
return inertia('ModuleName/PageFolder/Index', [
    'data' => $data,
]);

// Maps to: modules/{module}/resources/js/Pages/PageFolder/Index.vue
```

### Component Organization

```
Components/
├── Actions/           # Action buttons, menus
├── Forms/             # Form components
├── Tables/            # Table components
├── Dialogs/           # Modal dialogs
└── {Feature}*.vue     # Feature-specific components
```

### Shared vs Module Components

- **Shared** (`resources/js/Components/`): Used across multiple modules
- **Module** (`modules/{module}/resources/js/Components/`): Module-specific only

Never duplicate - if needed in multiple modules, move to shared.

## External Contract Pattern

When you need functionality from main app:

### Step 1: Define Interface in Module

```php
// modules/{module}/src/Contracts/External/UserContract.php
namespace Modules\{Module}\Contracts\External;

interface UserContract
{
    public function find(int $id): ?array;
    public function search(string $keyword): array;
}
```

### Step 2: Implement Adapter in Main App

```php
// app/Services/{Module}/UserAdapter.php
namespace App\Services\{Module};

use App\Models\User;
use Modules\{Module}\Contracts\External\UserContract;

class UserAdapter implements UserContract
{
    public function find(int $id): ?array
    {
        return User::find($id)?->toArray();
    }
}
```

### Step 3: Bind in Service Provider

```php
// app/Providers/{Module}ServiceProvider.php
$this->app->bind(
    \Modules\{Module}\Contracts\External\UserContract::class,
    \App\Services\{Module}\UserAdapter::class
);
```

## Code Quality

### SOLID Principles
- **S**: One class = one responsibility
- **O**: Extend, don't modify
- **L**: Subtypes are substitutable
- **I**: Many specific interfaces > one fat interface
- **D**: Depend on abstractions

### Anti-Patterns to Avoid
- God classes (>300 lines)
- Static calls (`Class::method()`)
- Fat controllers (business logic in controllers)
- Raw arrays (use DTOs)
- Magic values (use config/enums)

## Dependency Injection

```php
// GOOD - Constructor injection
public function __construct(
    private UserContract $userService,
    private InvoiceRepository $repository,
) {}

// BAD - Static calls
$user = User::find($id);
$result = SomeService::process($data);
```

## Testing Requirements

| Module Status | Coverage |
|---------------|----------|
| Complete | 80% |
| Partial | 60% |
| Critical paths | 90%+ |

## Scope Restriction

When working as a module agent:
- Only modify files within `modules/{module}/`
- For external dependencies, create contracts in `Contracts/External/`
- For adapters, instruct user to create in `app/Services/{Module}/`
