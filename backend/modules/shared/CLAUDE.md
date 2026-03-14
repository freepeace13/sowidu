# Shared Module

Infrastructure and utility code shared across modules.

## Status: Utility Module

This is NOT a feature module - it provides shared infrastructure.

## Structure

```
shared/src/
├── SharedServiceProvider.php
├── Enums/                    # Shared enumerations
├── Http/
│   └── (base classes, middleware)
├── Traits/                   # Reusable traits
└── Transformer.php           # Base transformer class
```

## Also: Shared (uppercase)

There's also a `Shared/` module with uppercase:

```
Shared/src/
├── Actions/
│   └── Notifications/
├── Controllers/
├── Enums/
├── Middlewares/
├── Models/
│   ├── Concerns/
│   └── Relations/
├── Services/
├── Traits/
└── Transformers/
```

## Purpose

Provides base classes and utilities for other modules:

### Base Classes
- `Transformer.php` - Base transformer all modules extend
- Base controllers
- Shared middleware

### Shared Enums
Common enumerations used across modules.

### Shared Traits
Reusable trait classes.

### Shared Models
Models used by multiple modules (with Concerns and Relations).

## AI Guidelines

When working here:
1. Only add truly shared/cross-cutting code
2. Don't add feature-specific code
3. Keep utilities generic and reusable
4. Other modules should depend on shared, not vice versa

## Usage

Other modules import from shared:
```php
use Modules\Shared\Transformer;
use Modules\Shared\Traits\SomeTrait;
use Modules\Shared\Enums\SomeEnum;
```
