# Create Module

Scaffold a new module with the proper structure following project conventions.

## Instructions

When this command is invoked with a module name (e.g., `/module-create orders`):

1. **Ask for module name** if not provided
2. **Create the full module structure** following the invoicify pattern
3. **Generate boilerplate files**:
   - composer.json with PSR-4 autoload
   - ServiceProvider with standard registrations
   - Config file with route prefix and middleware
   - Routes file
   - README.md
4. **Remind user** to register the service provider

## Module Structure to Create

```
modules/{module-name}/
├── composer.json
├── config/
│   └── {module-name}.php
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── css/
│   │   └── {module-name}.css       # Module styles
│   ├── js/
│   │   ├── Pages/                  # Inertia pages
│   │   │   └── {Feature}/
│   │   │       └── Index.vue
│   │   ├── Components/             # Vue components
│   │   │   ├── Forms/
│   │   │   └── Actions/
│   │   ├── Composables/            # Vue composables
│   │   ├── Services/               # JS services (EventBus, etc.)
│   │   ├── bootstrap/              # JS bootstrapping
│   │   │   └── index.js
│   │   ├── types/                  # TypeScript types
│   │   └── main.js                 # Entry point
│   └── views/
│       ├── app.blade.php           # Inertia root view
│       └── components/             # Blade components
│           └── pdf/                # PDF templates (if needed)
├── routes/
│   └── web.php
├── src/
│   ├── {ModuleName}ServiceProvider.php
│   ├── Actions/
│   ├── Contracts/
│   │   ├── Actions/
│   │   └── External/
│   ├── Data/
│   ├── Enums/
│   ├── Events/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   ├── Requests/
│   │   └── Resources/
│   ├── Models/
│   ├── Policies/
│   ├── Services/
│   ├── Support/
│   ├── Traits/
│   └── Transformers/
├── tests/
│   ├── Feature/
│   └── Unit/
├── CLAUDE.md
└── README.md
```

## Boilerplate: composer.json

```json
{
    "name": "modules/{module-name}",
    "description": "{Module} module for Sowidu",
    "type": "library",
    "require": {},
    "autoload": {
        "psr-4": {
            "Modules\\{ModuleName}\\": "src/",
            "Modules\\{ModuleName}\\Database\\Factories\\": "database/factories/",
            "Modules\\{ModuleName}\\Database\\Seeders\\": "database/seeders/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Modules\\{ModuleName}\\Tests\\": "tests/"
        }
    },
    "extra": {
        "laravel": {
            "providers": [
                "Modules\\{ModuleName}\\{ModuleName}ServiceProvider"
            ]
        }
    }
}
```

## Boilerplate: ServiceProvider

```php
<?php

namespace Modules\{ModuleName};

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class {ModuleName}ServiceProvider extends ServiceProvider
{
    public $bindings = [
        // Contracts\Actions\SomeActionContract::class => Actions\SomeAction::class,
    ];

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/{module-name}.php', '{module-name}');
    }

    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerViews();
        $this->registerPolicies();

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }

    protected function registerRoutes(): void
    {
        Route::group([
            'prefix' => config('{module-name}.prefix'),
            'middleware' => config('{module-name}.middleware'),
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    protected function registerViews(): void
    {
        // Register Blade views with namespace (use as: @include('{module-name}::view-name'))
        $this->loadViewsFrom(__DIR__ . '/../resources/views', '{module-name}');

        // Register anonymous Blade components (use as: <x-{module-name}::component-name />)
        \Illuminate\Support\Facades\Blade::anonymousComponentPath(
            __DIR__ . '/../resources/views/components',
            '{module-name}'
        );
    }

    protected function registerPolicies(): void
    {
        // Gate::policy(Model::class, ModelPolicy::class);
    }
}
```

## Boilerplate: Config

```php
<?php

return [
    'prefix' => '{module-name}',
    'domain' => env('{MODULE}_DOMAIN'),
    'middleware' => ['web', 'auth'],
];
```

## Boilerplate: Inertia Root View (app.blade.php)

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - {ModuleName}</title>
    @vite(['modules/{module-name}/resources/js/main.js', 'modules/{module-name}/resources/css/{module-name}.css'])
    @inertiaHead
</head>
<body>
    @inertia
</body>
</html>
```

## Boilerplate: Vue Page Component

```vue
<template>
    <div>
        <h1>{{ title }}</h1>
    </div>
</template>

<script setup>
defineProps({
    title: String,
});
</script>
```

## Boilerplate: main.js Entry Point

```javascript
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

createInertiaApp({
    resolve: (name) => resolvePageComponent(
        `./Pages/${name}.vue`,
        import.meta.glob('./Pages/**/*.vue')
    ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});
```

## Views Usage

### Blade Views
```php
// In controller
return view('{module-name}::app');

// Include partial
@include('{module-name}::partials.header')
```

### Blade Components
```blade
{{-- Use anonymous components from module --}}
<x-{module-name}::pdf.invoice :data="$invoice" />
<x-{module-name}::pdf.table.item :item="$item" />
```

### Inertia Pages
```php
// In controller
return inertia('{ModuleName}/Invoices/Show/index', [
    'invoice' => $invoice,
]);
```

## Boilerplate: Vite Config ({module-name}.vite.mjs)

Each module needs its own Vite config file:

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

## Boilerplate: core.js Entry Point

```javascript
// modules/{module-name}/resources/js/core.js
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

createInertiaApp({
    resolve: (name) => resolvePageComponent(
        `./Pages/${name}.vue`,
        import.meta.glob('./Pages/**/*.vue')
    ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});
```

## Register Module in Main vite.config.mjs

Add the module to the main `vite.config.mjs`:

```javascript
// 1. Import at top of file
import {moduleName}Config from './modules/{module-name}/{module-name}.vite.mjs'

// 2. Add alias in resolve.alias section
...('{moduleName}Config?.alias || {}),

// 3. Add inputs in laravel plugin input array
...({moduleName}Config?.input || []),
```

**Example for a new "orders" module:**
```javascript
// vite.config.mjs
import ordersConfig from './modules/orders/orders.vite.mjs'

export default defineConfig({
    resolve: {
        alias: {
            // ... existing aliases
            '@Orders': path.resolve(__dirname, './modules/orders/resources/js'),
            ...(ordersConfig?.alias || {}),
        },
    },
    plugins: [
        laravel({
            input: [
                // ... existing inputs
                ...(ordersConfig?.input || []),
            ],
            // ...
        }),
    ],
})
```

## Using Module Alias in Vue Components

```javascript
// Import from module using alias
import SomeComponent from '@{ModuleName}/Components/SomeComponent.vue'
import { useEventListener } from '@{ModuleName}/Composables/useEventListener'
```

## After Creation

Remind user to:
1. Register provider in `config/app.php` or rely on package auto-discovery
2. Run `composer dump-autoload`
3. Create adapters in `app/Services/{ModuleName}/` for external contracts
4. Register adapter bindings in `app/Providers/{ModuleName}ServiceProvider.php`
5. **Create `{module-name}.vite.mjs`** in module root
6. **Register module in `vite.config.mjs`** (import, alias, input)
7. Run `npm run dev` or `npm run build` to compile assets
