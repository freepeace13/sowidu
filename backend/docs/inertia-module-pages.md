# Inertia Module Page Resolution

This guide explains how modules can define Inertia pages using the `@module/PageName` pattern, which allows modules to have their own Vue pages that are resolved dynamically by the main application's Inertia resolver.

## Table of Contents

- [Overview](#overview)
- [File Structure](#file-structure)
- [Naming Conventions](#naming-conventions)
- [Controller Usage](#controller-usage)
- [Resolution Logic](#resolution-logic)
- [Layout Handling](#layout-handling)
- [Examples](#examples)
- [Troubleshooting](#troubleshooting)
- [Best Practices](#best-practices)

## Overview

The main application's Inertia resolver (`resources/js/core.js`) supports a custom module page resolution pattern that allows modules to define their own Vue pages without requiring separate Inertia app instances. This pattern uses the `@module/PageName` syntax to reference module pages.

### Why This Pattern Exists

- **Module Isolation**: Modules can maintain their own page components without polluting the main app's page directory
- **Dynamic Resolution**: All module pages are discovered automatically using Vite's glob patterns
- **Consistent Layouts**: Module pages automatically receive the correct layout (AuthLayout or GuestLayout)
- **Fallback Support**: If a module page isn't found, the resolver falls back to main app pages

## File Structure

Module pages must be placed in a specific directory structure:

```
modules/
└── {module-name}/
    └── resources/
        └── js/
            └── Pages/
                ├── Index.vue          # Simple page
                ├── Show.vue           # Simple page
                └── Subdirectory/      # Nested pages
                    └── Index.vue
```

### Required Path

All module pages must be located at:
```
modules/{module-name}/resources/js/Pages/{page-path}.vue
```

The resolver uses the glob pattern `../../modules/*/resources/js/Pages/**/*.vue` to discover all module pages at build time.

## Naming Conventions

### Module Name Format

- **Case**: Module name in the `@module/PageName` pattern is **case-insensitive**
- **Format**: Use lowercase in the pattern (e.g., `@chatly/Index`, not `@Chatly/Index`)
- **Example**: `@chatly/Index` will match `modules/chatly/resources/js/Pages/Index.vue` regardless of the module directory's actual case

### Page Name Format

- **Case**: Page path is **case-sensitive** after the module name
- **Format**: Use PascalCase matching the Vue file name
- **File Extension**: Always `.vue`
- **Examples**:
  - `@chatly/Index` → `modules/chatly/resources/js/Pages/Index.vue`
  - `@chatly/Show` → `modules/chatly/resources/js/Pages/Show.vue`

### Nested Pages

Nested pages use forward slashes (`/`) to represent directory structure:

- **Pattern**: `@module/Subdirectory/PageName`
- **File Path**: `modules/{module}/resources/js/Pages/Subdirectory/PageName.vue`
- **Example**: `@todos/Board/Index` → `modules/todos/resources/js/Pages/Board/Index.vue`

## Controller Usage

To render a module page from a controller, use the `@module/PageName` pattern with `Inertia::render()`:

```php
<?php

namespace Modules\Chatly\Http\Controllers\Inertia;

use Inertia\Inertia;
use Illuminate\Http\Request;

class ConversationController extends BaseController
{
    public function index(Request $request)
    {
        return Inertia::render('@chatly/Index', [
            'title' => 'Chat',
            'conversations' => $this->chatRepository->conversations()->get(),
        ]);
    }

    public function show(Request $request, $id)
    {
        return Inertia::render('@chatly/Show', [
            'title' => 'Chat',
            'conversation' => $this->chatRepository->find($id),
        ]);
    }
}
```

### Key Points

1. **Module Prefix**: Always prefix with `@` followed by the module name (lowercase)
2. **Page Name**: Use PascalCase matching the Vue file name
3. **Props**: Pass props as the second argument, same as regular Inertia pages
4. **Lazy Props**: Lazy props are supported just like regular Inertia pages

## Resolution Logic

The resolution logic is implemented in `resources/js/core.js`. Here's how it works:

### Step 1: Pre-load Module Pages

At application startup, all module pages are discovered using Vite's `import.meta.glob`:

```javascript
const modulePages = import.meta.glob(
    '../../modules/*/resources/js/Pages/**/*.vue',
)
```

This creates a map of all module page paths to their import functions.

### Step 2: Check for Module Page Pattern

When Inertia requests a page, the resolver checks if the name starts with `@`:

```javascript
if (name.startsWith('@')) {
    // Module page resolution logic
}
```

### Step 3: Parse Module and Page Path

The resolver extracts the module name and page path:

```javascript
const parts = name.substring(1).split('/')
const moduleName = parts[0].toLowerCase()  // Case-insensitive
const pagePath = parts.slice(1).join('/')  // Case-sensitive
```

### Step 4: Find Matching Page

The resolver searches for a matching page key using:

1. **Path Normalization**: Handles both `/` and `\` path separators
2. **Case-Insensitive Module Matching**: Module name comparison is case-insensitive
3. **Case-Sensitive Page Matching**: Page path comparison is case-sensitive
4. **Fallback Matching**: If exact match fails, checks if key ends with expected path

### Step 5: Load and Return Page

If a match is found, the page is dynamically imported and returned:

```javascript
const page = await modulePages[pageKey]()
```

### Step 6: Fallback to Main App

If no module page is found, the resolver falls back to main app pages:

```javascript
const page = await resolvePageComponent(
    `./Pages/${name}.vue`,
    import.meta.glob('./Pages/**/*.vue'),
)
```

A warning is logged in the console when fallback occurs.

## Layout Handling

Module pages automatically receive layout assignment based on their name:

### Default Layouts

- **Auth Pages**: Pages starting with `Auth` receive `GuestLayout`
- **Other Pages**: All other pages receive `AuthLayout` by default

### Layout Logic

```javascript
page.default.layout = pagePath.startsWith('Auth')
    ? GuestLayout
    : page.default.layout || AuthLayout
```

### Custom Layouts

You can override the default layout in your Vue component:

```vue
<script>
import CustomLayout from '@/Layouts/CustomLayout.vue'

export default {
    layout: CustomLayout,
    // ... component options
}
</script>
```

## Examples

### Simple Page

**Controller** (`modules/chatly/src/Http/Controllers/Inertia/ConversationController.php`):
```php
return Inertia::render('@chatly/Index', [
    'title' => 'Chat',
    'conversations' => $conversations,
]);
```

**Vue Component** (`modules/chatly/resources/js/Pages/Index.vue`):
```vue
<template>
    <div>
        <h1>{{ title }}</h1>
        <!-- Page content -->
    </div>
</template>

<script>
export default {
    props: {
        title: String,
        conversations: Array,
    },
}
</script>
```

### Nested Page

**Controller** (`modules/catalog/src/Http/Controllers/Inertia/CatalogItemController.php`):
```php
return Inertia::render('@catalog/Index', [
    'items' => $items,
    'filters' => $request->only(['q', 'type']),
]);
```

**Vue Component** (`modules/catalog/resources/js/Pages/Index.vue`):
```vue
<template>
    <div>
        <h1>Catalog Items</h1>
        <!-- Catalog items list -->
    </div>
</template>
```

### Deeply Nested Page

**Pattern**: `@todos/Board/Index`

**File Path**: `modules/todos/resources/js/Pages/Board/Index.vue`

**Controller**:
```php
return Inertia::render('@todos/Board/Index', [
    'board' => $board,
]);
```

## Troubleshooting

### Page Not Found

**Symptom**: Console warning "Module page not found: @module/PageName, falling back to main app pages"

**Possible Causes**:
1. **Incorrect Module Name**: Module name in pattern doesn't match directory name (case-insensitive)
2. **Incorrect Page Path**: Page path doesn't match file structure (case-sensitive)
3. **File Not in Correct Location**: Page file not in `modules/{name}/resources/js/Pages/`
4. **Build Cache**: Vite hasn't picked up the new file

**Solutions**:
- Verify module name matches directory name (case-insensitive)
- Verify page path matches file structure exactly (case-sensitive)
- Check file is in `modules/{module-name}/resources/js/Pages/`
- Clear Vite cache and rebuild: `npm run build` or restart dev server

### Wrong Layout Applied

**Symptom**: Page uses incorrect layout (AuthLayout instead of GuestLayout or vice versa)

**Solutions**:
- Check if page name starts with `Auth` for GuestLayout
- Override layout in Vue component if needed
- Verify layout imports are correct in `resources/js/core.js`

### Case Sensitivity Issues

**Symptom**: Page resolves incorrectly or not at all

**Solutions**:
- Module name: Use lowercase in pattern (`@chatly`, not `@Chatly`)
- Page path: Match file structure exactly, including case (`Index.vue`, not `index.vue`)
- Directory names: Match exactly, including case (`Board/Index.vue`, not `board/index.vue`)

### Path Separator Issues

**Symptom**: Page not found on Windows

**Note**: The resolver handles both `/` and `\` separators automatically, so this shouldn't be an issue. If problems persist, ensure you're using forward slashes in the pattern.

## Best Practices

### 1. Consistent Naming

- Use PascalCase for Vue component files (`Index.vue`, `Show.vue`)
- Use lowercase for module name in pattern (`@chatly`, not `@Chatly`)
- Match file names exactly in the pattern

### 2. Directory Organization

- Keep pages flat when possible (`Index.vue`, `Show.vue`)
- Use nested directories for related pages (`Board/Index.vue`, `Board/Show.vue`)
- Group related pages in subdirectories

### 3. Controller Organization

- Place Inertia controllers in `modules/{name}/src/Http/Controllers/Inertia/`
- Use descriptive controller names matching the resource
- Keep controller logic minimal, delegate to Actions

### 4. Page Component Structure

- Follow Vue.js best practices
- Use props for data passed from controller
- Define prop types for better development experience
- Use TypeScript if available for type safety

### 5. Layout Considerations

- Use default layouts when possible (AuthLayout for authenticated pages)
- Override layout only when necessary
- Keep custom layouts in main app (`resources/js/Layouts/`)

### 6. Testing

- Test page resolution in development
- Verify fallback behavior when page doesn't exist
- Test nested page resolution
- Verify layout assignment

## Related Documentation

- [Module Organization](./DOCUMENTATION_GUIDELINES.md) - Module architecture patterns
- [Directory Structure](./directory-structure.md) - Project layout conventions
- [Inertia.js Documentation](https://inertiajs.com/) - Official Inertia.js guide

## Implementation Reference

The resolver implementation can be found in:
- **Main Resolver**: `resources/js/core.js` (lines 50-109)
- **Example Controllers**:
  - `modules/chatly/src/Http/Controllers/Inertia/ConversationController.php`
  - `modules/catalog/src/Http/Controllers/Inertia/CatalogItemController.php`
- **Example Pages**:
  - `modules/chatly/resources/js/Pages/Index.vue`
  - `modules/chatly/resources/js/Pages/Show.vue`
  - `modules/todos/resources/js/Pages/Board/Index.vue`

