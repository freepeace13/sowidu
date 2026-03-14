# Monorepo Structure & Modularization Guide

This document outlines the monorepo structure, module organization, and best practices for the Sowidu project.

## 📁 Monorepo Structure

```
Sowidu/                          # Monorepo root
├── apps/
│   └── sw-web/                  # Main Laravel application
│       ├── app/                  # Application code
│       ├── modules/              # Feature modules
│       │   ├── chatly/
│       │   ├── invoicify/
│       │   ├── offer/
│       │   ├── shared/
│       │   └── shared-data/
│       ├── packages/             # Local packages (app-specific)
│       │   ├── Countries/
│       │   ├── Data/
│       │   ├── MediaLibrary/
│       │   ├── RestApi/
│       │   └── Urn/
│       └── composer.json         # Main app dependencies
│
└── packages/                     # Shared packages (monorepo-wide)
    ├── active-status/
    ├── addressable/
    ├── avatarable/
    ├── contacts/
    └── translation/
```

## 🎯 Module vs Package Distinction

### Modules (`apps/sw-web/modules/`)
- **Purpose**: Feature areas of the product
- **Scope**: Application-specific business logic
- **Structure**: Full Laravel module (routes, controllers, models, migrations, views)
- **Examples**: `chatly`, `invoicify`, `offer`
- **Dependencies**: Can depend on packages, but not other modules directly
- **Integration**: Integrated via service providers and external contracts

### Local Packages (`apps/sw-web/packages/`)
- **Purpose**: Reusable libraries specific to this application
- **Scope**: Application-specific utilities
- **Structure**: Laravel package structure
- **Examples**: `MediaLibrary`, `RestApi`, `Urn`
- **Usage**: Used by modules and main app

### Shared Packages (`packages/`)
- **Purpose**: Reusable libraries across multiple applications
- **Scope**: Generic, framework-agnostic utilities
- **Structure**: Standalone package structure
- **Examples**: `addressable`, `avatarable`, `contacts`
- **Usage**: Can be used by any app in the monorepo

## 📦 Module Structure Standard

Every module MUST follow this structure:

```
modules/{module-name}/
├── composer.json                 # Module dependencies
├── README.md                     # Module overview
├── agents.md                     # Agent patterns (if applicable)
├── config/                       # Module configuration
│   └── {module-name}.php
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── docs/                         # Module-specific documentation
│   ├── module-guide.md           # Index/navigation
│   ├── architecture.md
│   ├── api.md
│   └── ...
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/                       # Module routes
│   └── web.php
├── src/
│   ├── Actions/                  # Business logic
│   ├── Contracts/                # Interfaces
│   │   └── External/             # External dependencies
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   ├── Request/
│   │   └── Resource/
│   ├── Models/                   # Domain models
│   ├── Providers/                # Service providers
│   └── ...
├── tests/
│   ├── Unit/
│   ├── Feature/
│   └── Integration/
└── pint.json                     # Code style config (optional)
```

## 📋 Module composer.json Standard

Every module MUST have a `composer.json` with this structure:

```json
{
    "name": "sowidu/{module-name}",
    "description": "Module description",
    "version": "1.x-dev",
    "type": "library",
    "require": {
        "php": "^8.0",
        "sowidu/shared": "1.x-dev"
    },
    "autoload": {
        "psr-4": {
            "Modules\\{ModuleName}\\": "src/"
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
                "Modules\\{ModuleName}\\Providers\\{ModuleName}ServiceProvider"
            ]
        }
    },
    "config": {
        "optimize-autoloader": true,
        "sort-packages": true
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
```

## 🔗 Dependency Management

### Module Dependencies
- Modules can depend on:
  - ✅ Shared packages (`sowidu/shared`, `sowidu/addressable`, etc.)
  - ✅ Local packages (via autoload)
  - ✅ External packages (via composer)
  - ❌ Other modules (use external contracts instead)

### Package Dependencies
- Packages can depend on:
  - ✅ Other packages
  - ✅ External packages
  - ❌ Modules
  - ❌ Main app code

## 🔌 External Contracts Pattern

Modules MUST use external contracts for dependencies on main app:

1. **Define Interface** in `modules/{name}/src/Contracts/External/`
2. **Implement Adapter** in `app/Services/{ModuleName}/`
3. **Register Binding** in service provider
4. **Inject Contract** via constructor

See: `.cursor/rules/module-organization.mdc` for details.

## 📝 Composer Repository Configuration

The main `composer.json` should include:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./modules/*",
            "options": {
                "symlink": true
            }
        },
        {
            "type": "path",
            "url": "../../packages/*",
            "options": {
                "symlink": false
            }
        }
    ]
}
```

## 🚀 Development Workflow

### Adding a New Module
1. Create directory: `modules/{module-name}/`
2. Create `composer.json` following standard
3. Create service provider
4. Register in main app service provider
5. Add documentation

### Adding a New Package
1. Determine scope:
   - App-specific → `apps/sw-web/packages/`
   - Shared → `packages/`
2. Create package structure
3. Add to composer repositories
4. Update autoload if needed

### Updating Dependencies
1. Update module/package `composer.json`
2. Run `composer update` in main app
3. Test integration
4. Update documentation

## 🔐 Authentication Setup

For GitHub authentication (required for private packages):

1. Generate a GitHub Personal Access Token with `repo` scope
2. Add to `auth.json`:
```json
{
    "github-oauth": {
        "github.com": "ghp_YOUR_TOKEN_HERE"
    }
}
```

**Note**: Never commit `auth.json` to version control. Use environment variables or secure storage.

## 📚 Documentation Standards

- **Module docs**: `modules/{name}/docs/`
- **Integration docs**: `docs/{module-name}/`
- **Cross-cutting docs**: `docs/`
- **Package docs**: `packages/{name}/docs/` or `packages/{name}/README.md`

See: `docs/DOCUMENTATION_GUIDELINES.md` for complete rules.

## ✅ Module Checklist

When creating or updating a module, ensure:

- [ ] `composer.json` follows standard structure
- [ ] Service provider is registered
- [ ] External contracts are used (no direct App imports)
- [ ] README.md exists with overview
- [ ] Documentation is in `docs/` folder
- [ ] Tests are in `tests/` folder
- [ ] Routes are in `routes/` folder
- [ ] Config is in `config/` folder
- [ ] Follows naming conventions (PascalCase for classes, kebab-case for files)

## 🔄 Migration Guide

### Migrating Existing Modules

1. **Standardize composer.json**
   - Ensure proper naming (`sowidu/{name}`)
   - Add service provider to `extra.laravel.providers`
   - Standardize autoload paths

2. **Verify Structure**
   - Check directory structure matches standard
   - Ensure external contracts are used
   - Verify no direct App imports

3. **Update Documentation**
   - Add/update README.md
   - Ensure docs/ folder exists
   - Update module-guide.md

4. **Test Integration**
   - Run `composer update`
   - Test module functionality
   - Verify service provider registration

## 📖 Related Documentation

- [Module Organization](./.cursor/rules/module-organization.mdc)
- [Action Classes Standards](./.cursor/rules/action-classes.mdc)
- [Documentation Guidelines](./DOCUMENTATION_GUIDELINES.md)
- [Directory Structure](./directory-structure.md)

---

**Last Updated**: 2024-12-10
**Maintained By**: Development Team

