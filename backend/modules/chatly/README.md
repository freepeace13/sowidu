# Chatly Module

Chatly encapsulates the messaging experience for Sowidu. It provides conversation management, participant controls, and message delivery APIs that can be embedded in multiple surfaces of the product.

## Key Features
- Conversation lifecycle management (create, update, archive).
- Participant onboarding, removal, and role handling.
- Message creation and deletion with configurable pagination defaults.
- Route registration via `ChatlyServiceProvider` with adjustable prefixes and middleware.

## 📚 Documentation

### Module Documentation (`docs/`)
- **[Module Guide](docs/module-guide.md)** – Complete documentation index
- **[Overview](docs/overview.md)** – High-level concepts and workflow
- **[Architecture](docs/architecture.md)** – Ports & Adapters pattern
- **[External Contracts](docs/external-contracts.md)** – Outgoing ports (external system interfaces)
- **[Features](docs/features.md)** – Core capabilities
- **[API Reference](docs/api.md)** – REST endpoints and payloads
- **[Database Schema](docs/database.md)** – Chat tables structure
- **[Testing Guide](docs/testing-guide.md)** – How to test Chatly
- **[Integration Guide](docs/integration-guide.md)** – Integrating with main app
- **[Usage](docs/usage.md)** – Invocation patterns and tooling
- **[Installation](docs/installation.md)** – Setup requirements
- **[Agents](agents.md)** – Orchestration patterns

### Main App Integration (`../../docs/chatly/`)
For setup and configuration in the main Sowidu application:
- **[Setup Guide](../../docs/chatly/setup.md)** – Installation and verification
- **[Adapters Implementation](../../docs/chatly/adapters-implementation.md)** – Adapter details
- **[Refactoring Summary](../../docs/chatly/refactoring-summary.md)** – Complete project overview

### Service Adapters (`../../app/Services/Chat/`)
- **[Adapter README](../../app/Services/Chat/README.md)** – Adapter class documentation

## Directory Guide
- `config/`: Module-level configuration published through `chatly.php`.
- `database/`: Migrations, factories, and seeds scoped to chat data models.
- `resources/`: Blade views, translations, and assets owned by Chatly.
- `routes/`: Web routes loaded when `chatly.should_load_routes` is enabled.
- `src/Actions`: Invokable operations for conversation and message management.
- `src/Http`: Controllers, requests, and resources that expose the HTTP surface.
- `src/Providers`: Service provider responsible for bootstrapping the module.

## Usage Notes
- Register the service provider in the application bootstrap if not auto-discovered.
- Override the config values by copying `config/chatly.php` into the host app's config directory and adjusting prefixes, middleware, or pagination defaults.
- Compose actions or agents (see `agents.md`) in your controllers and jobs to keep orchestration logic tidy.

## Development Checklist
1. Add new database artifacts under `database/` and run the migrations within the module context.
2. Keep side-effectful logic inside Actions; controllers should orchestrate those actions.
3. Document any new workflow inside `docs/` and update `agents.md` when introducing or modifying agents.
