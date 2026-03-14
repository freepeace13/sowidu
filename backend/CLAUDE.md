# Sowidu Project Guidelines

## Project Overview

Laravel + Vue.js (Inertia) application using Ports & Adapters (Hexagonal) architecture.

**Reference module**: `modules/invoicify` - use as template for new modules.

## Critical Rules

### Module Isolation (NEVER violate)

```php
// BAD - Module directly imports main app
use App\Models\User; // NEVER do this in modules!

// GOOD - Module uses contract
use Modules\Chatly\Contracts\External\UserSearchContract;
```

### Type Safety

All PHP files MUST have:
```php
<?php

declare(strict_types=1);
```

### Dependency Injection

```php
// GOOD - Constructor injection
public function __construct(protected UserSearchContract $userSearch) {}

// BAD - Static calls
UrnManager::resolve($urn);
```

## Commands

Use `./vendor/bin/sail` for artisan commands.

### Modularization

| Command | Purpose |
|---------|---------|
| `/module-guide` | Architecture patterns |
| `/module-create` | Scaffold new module |
| `/module-refactor` | Move code to module |
| `/module-status` | Check progress |

### Code Quality (Planned)

| Command | Purpose |
|---------|---------|
| `/code-audit` | Full codebase audit |
| `/find-duplicates` | Find duplicated code |
| `/find-unused` | Find dead code |
| `/find-violations` | Find architecture violations |
| `/security-scan` | Security vulnerabilities |

### Documentation (Planned)

| Command | Purpose |
|---------|---------|
| `/generate-user-guide` | User documentation |
| `/generate-tutorial` | Step-by-step tutorial |
| `/translate-docs` | Translate to language |
| `/generate-api-docs` | API documentation |

### Context Loading

| Command | Loads |
|---------|-------|
| `/context-architecture` | Module structure, contracts, Vite |
| `/context-quality` | SOLID, design patterns, smells |
| `/context-security` | Auth, validation, XSS, SQL injection |
| `/context-testing` | Coverage, test patterns |
| `/context-database` | Migrations, indexes, queries |
| `/context-api` | Response formats, status codes |
| `/context-git` | Branches, commits, PR checklist |

## Quick Reference

### Module Structure

```
modules/{name}/
├── src/
│   ├── {Module}ServiceProvider.php
│   ├── Actions/           # Business logic
│   ├── Contracts/External/ # Ports to outside world
│   ├── Data/              # DTOs
│   ├── Http/Controllers/
│   └── Models/
├── resources/js/          # Vue components
├── routes/web.php
└── tests/
```

### External Contracts Pattern

1. Define interface in `modules/{module}/src/Contracts/External/`
2. Implement adapter in `app/Services/{Module}/`
3. Bind in `app/Providers/{Module}ServiceProvider.php`

### Class Naming

| Type | Convention |
|------|------------|
| Actions | `{Verb}{Noun}Action` |
| Contracts | `{Verb}{Noun}Contract` |
| Adapters | `{Name}Adapter` |

### Test Requirements

| Module Status | Coverage |
|---------------|----------|
| Complete | 80% |
| Partial | 60% |
| New | 80% before merge |

Critical paths: **90%+**

## Context Files

Detailed documentation in `.claude/context/`:

| File | Contents |
|------|----------|
| `architecture.md` | Module structure, views, Vite, contracts |
| `code-quality.md` | SOLID, patterns, anti-patterns, smells |
| `security.md` | Auth, validation, XSS, SQL injection, errors |
| `testing.md` | Coverage, test examples, best practices |
| `database.md` | Migrations, indexes, N+1, caching |
| `api.md` | Response formats, status codes, resources |
| `git.md` | Branches, commits, PR checklist |
| `status.md` | Current module status, tasks, candidates |

## Current Priority Tasks

1. Add external contracts to todos & worklogs
2. Create Vite configs for chatly & catalog
3. Create Documentation module (German support)

Run `/module-status` for full details.
