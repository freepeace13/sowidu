# Sowidu Documentation

Welcome to the Sowidu documentation hub. This directory contains project-wide documentation and guides.

## 📚 Documentation Index

### Getting Started
- **[Setup Guide](../SETUP.md)** - Complete setup instructions for local development using Laravel Sail
- **[Main README](../README.md)** - Project overview and quick start
- **[Development Workflow](../DEV.md)** - Git workflow and contribution guidelines
- **[Deployment Guide](../DEPLOY.md)** - Deployment procedures and server setup

### Architecture & Organization
- **[Directory Structure Guide](./directory-structure.md)** - Project layout conventions and where to put code
- **[Module Organization](./.cursor/rules/module-organization.mdc)** - Module architecture and external contracts pattern
- **[Action Classes Standards](../.cursor/rules/action-classes.mdc)** - Standards for Action class implementation
- **[Inertia Module Pages](./inertia-module-pages.md)** - Custom module Inertia page resolution guide

### Module-Specific Documentation
- **[Chatly Module](../modules/chatly/docs/)** - Real-time chat functionality
- **[Offer Module](../modules/offer/docs/)** - Offer management system

### Packages
Located in `/packages/`:
- **Countries** - Country data and utilities
- **Data** - Data structures and utilities
- **MediaLibrary** - Media management
- **RestApi** - REST API utilities
- **Urn** - URN handling

## 🏗️ Architecture Patterns

### Hexagonal Architecture (Ports and Adapters)

Modules in Sowidu follow the Hexagonal Architecture pattern for complete decoupling:

1. **Define outgoing ports** (interfaces) in `modules/{name}/src/Contracts/External/`
2. **Implement adapters** in `app/Services/{ModuleName}/`
3. **Register bindings** in service providers (e.g., `app/Providers/ChatServiceProvider.php`)
4. **Inject contracts** via constructor in module code

**Important**: Modules MUST NEVER directly import `App\Models\*` or `App\Services\*`.

### Action Classes

All action classes must:
- Implement a corresponding interface from `app/Contracts/Actions/`
- Accept `$teamId` and `$errorBag` parameters
- Return data/models (NOT HTTP responses)
- Be simple and reusable across contexts

See [Action Classes Standards](../.cursor/rules/action-classes.mdc) for complete guidelines.

## 📖 Documentation Guidelines

### Where to Place Documentation

1. **Project-wide documentation** → `/docs/` (this folder)
2. **Module-specific documentation** → `/modules/{module-name}/docs/`
3. **Package-specific documentation** → `/packages/{package-name}/docs/`
4. **Main app integration docs** → `/docs/` with appropriate naming

### File Naming

- Use `kebab-case.md` for all documentation files
- Always update index/README files when adding new documentation
- Complete rules: See `docs/DOCUMENTATION_GUIDELINES.md` (if exists)

## 🔧 Development Tools

### Laravel Sail

This project uses Laravel Sail for local development. Key commands:

```bash
# Start services
./vendor/bin/sail up -d

# Run artisan commands
./vendor/bin/sail artisan [command]

# Run tests
./vendor/bin/sail test

# Access container shell
./vendor/bin/sail shell
```

See [SETUP.md](./SETUP.md) for complete Sail usage.

### Makefile Commands

The project includes a Makefile with common tasks:

```bash
make setup     # Initial project setup
# (see Makefile for more commands)
```

## 🤝 Contributing

1. Read the [Development Workflow](../DEV.md)
2. Follow the [Directory Structure Guide](./directory-structure.md)
3. Ensure your code follows the [Action Classes Standards](../.cursor/rules/action-classes.mdc)
4. Update relevant documentation when adding features
5. Keep module READMEs, `agents.md`, and docs in sync with code changes

### Git Branches

- `main` - Production
- `staging` - Staging

**Important**: 
- No development branch exists
- Create new branches from `main` for each PR
- Merge to `staging` for testing before production

## 📞 Support

For questions or issues:
1. Check the relevant module's documentation
2. Review the setup guide
3. Contact the development team

## 🔗 External Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sail Documentation](https://laravel.com/docs/sail)
- [Vue.js Documentation](https://vuejs.org)
- [Docker Documentation](https://docs.docker.com)

