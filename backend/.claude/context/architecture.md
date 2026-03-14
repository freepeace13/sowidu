# Architecture Context

Load this context with `/context-architecture` when working on module structure.

## Module Structure

Modules live in `modules/{module-name}/` and should be self-contained:

```
modules/{module-name}/
├── composer.json              # PSR-4: Modules\{ModuleName}\ => src/
├── config/{module}.php        # Route prefix, middleware config
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
│   └── web.php
├── src/
│   ├── {Module}ServiceProvider.php
│   ├── Actions/               # Business logic
│   ├── Contracts/             # Interfaces (ports)
│   │   └── External/          # Outgoing ports (external deps)
│   ├── Data/                  # DTOs
│   ├── Enums/
│   ├── Events/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/
│   │   └── Resources/         # API resources
│   ├── Models/
│   ├── Policies/
│   ├── Services/
│   ├── Support/
│   ├── Traits/
│   └── Transformers/
├── tests/
│   ├── Unit/
│   ├── Feature/
│   └── Integration/
├── docs/                      # Module documentation
└── README.md
```

**Reference module**: `modules/invoicify` - most mature, use as template.

## Module Resources (Views & Frontend)

Each module can have its own frontend assets in `resources/`:

```
resources/
├── css/
│   └── {module}.css           # Module styles
├── js/
│   ├── main.js                # Entry point
│   ├── Pages/                 # Inertia pages
│   ├── Components/            # Vue components
│   │   ├── Forms/
│   │   └── Actions/
│   ├── Composables/           # Vue composables
│   ├── Services/              # JS services
│   └── bootstrap/             # JS bootstrapping
└── views/
    ├── app.blade.php          # Inertia root view
    └── components/            # Blade components
```

### Registering Views in Service Provider

```php
protected function registerViews(): void
{
    // Blade views: use as view('{module}::view-name')
    $this->loadViewsFrom(__DIR__ . '/../resources/views', '{module}');

    // Anonymous components: use as <x-{module}::component />
    Blade::anonymousComponentPath(__DIR__ . '/../resources/views/components', '{module}');
}
```

### Using Module Views

```php
// Blade view
return view('invoicify::app');

// Blade component
<x-invoicify::pdf.invoice :data="$invoice" />

// Inertia page
return inertia('Invoicify/Invoices/Show/index', ['invoice' => $invoice]);
```

## Vite Configuration for Modules

Each module with frontend assets needs a Vite config file.

### 1. Create Module Vite Config

Create `modules/{module-name}/{module-name}.vite.mjs`:

```javascript
import path from 'path'
import { fileURLToPath } from 'url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

export default {
    input: [
        'modules/{module-name}/resources/js/core.js',
        'modules/{module-name}/resources/css/styles.css',
    ],
    alias: {
        '@{ModuleName}': path.resolve(__dirname, './resources/js'),
    },
}
```

### 2. Register in Main vite.config.mjs

```javascript
// Import at top
import {moduleName}Config from './modules/{module-name}/{module-name}.vite.mjs'

// Add to resolve.alias
'~{ModuleName}': path.resolve(__dirname, './modules/{module-name}/resources/js'),
...({moduleName}Config?.alias || {}),

// Add to laravel plugin input
...({moduleName}Config?.input || []),
```

### 3. Use Module Aliases in Vue

```javascript
import Component from '@ModuleName/Components/Component.vue'
import { composable } from '~ModuleName/Composables/composable'
```

## External Contracts Pattern (Critical)

Modules must NEVER directly import main app code. Use contracts instead:

### Rule 1: Define Interface in Module
Location: `modules/{module}/src/Contracts/External/`

```php
namespace Modules\Chatly\Contracts\External;

interface UserSearchContract
{
    public function search(string $keyword, array $filters, int $limit): array;
}
```

### Rule 2: Implement Adapter in Main App
Location: `app/Services/{ModuleName}/`

```php
namespace App\Services\Chat;

use Modules\Chatly\Contracts\External\UserSearchContract;

class UserSearchAdapter implements UserSearchContract
{
    public function search(string $keyword, array $filters, int $limit): array
    {
        // Implementation using App\Models\User, etc.
    }
}
```

### Rule 3: Register Binding in Service Provider
Location: `app/Providers/{ModuleName}ServiceProvider.php`

```php
$this->app->bind(
    \Modules\Chatly\Contracts\External\UserSearchContract::class,
    \App\Services\Chat\UserSearchAdapter::class
);
```

### Anti-Patterns to Avoid
```php
// BAD - Module directly imports main app
use App\Models\User; // NEVER do this in modules!

// GOOD - Module uses contract
use Modules\Chatly\Contracts\External\UserSearchContract;
```

## Action Class Standards

Actions must follow strict patterns:

### Required Structure
- MUST implement interface from `Contracts/Actions/`
- MUST accept `$teamId` parameter (nullable) for team context
- MUST accept `$errorBag` parameter (nullable) for validation
- MUST NOT return HTTP responses - return data/models instead

### Pattern
```php
namespace Modules\Invoice\Actions;

use Modules\Invoice\Contracts\Actions\GeneratesInvoicePdfContract;

class GenerateInvoicePdfAction implements GeneratesInvoicePdfContract
{
    public function generate($user, Invoice $invoice, $teamId = null, $errorBag = null): string
    {
        // Authorization
        // Validation
        // Business logic
        // Return data (NOT HTTP response)
    }
}
```

## File Naming Conventions

| Type | Convention | Example |
|------|------------|---------|
| PHP Classes | PascalCase.php | `UserSearchAdapter.php` |
| Vue Components | PascalCase.vue | `ChatsList.vue` |
| Documentation | kebab-case.md | `integration-guide.md` |
| Config files | kebab-case.php | `chatly.php` |
| Test files | {ClassName}Test.php | `CreateConversationTest.php` |

### Class Naming
- Actions: `{Verb}{Noun}Action` (e.g., `CreateConversationAction`)
- Contracts: `{Verb}{Noun}Contract` (e.g., `CreatesConversationContract`)
- Adapters: `{Name}Adapter` (e.g., `UserSearchAdapter`)
