# Chatly Documentation Map

Start here to find the right deep dive for the Chatly module.

## Architecture & Design
- [Architecture](architecture.md) – **Ports & Adapters (Hexagonal) architecture overview**.
- [Architecture Summary](architecture-summary.md) – Quick reference architecture guide.
- [External Contracts](external-contracts.md) – **Outgoing ports for external system dependencies**.
- [Agents](../agents.md) – Orchestration patterns and extension guidelines.

## Core Documentation
- [Overview](overview.md) – Conceptual summary and workflow walkthrough.
- [Features](features.md) – Overview of the module's core capabilities.
- [Usage](usage.md) – How to invoke actions, repositories, routes, and front-end assets.

## Integration & Setup
- [Integration Guide](integration-guide.md) – **Step-by-step integration with main application**.
- [Integration](integration.md) – How Chatly collaborates with other Sowidu services and shared packages.
- [Installation](installation.md) – Setup checklist, configuration overrides, and prerequisites.

## Testing
- [Testing Guide](testing-guide.md) – **How to write and run tests for Chatly**.
- [Refactoring Complete](refactoring-complete.md) – Summary of refactoring to use external contracts.

## Technical Reference
- [API](api.md) – REST endpoints, payload expectations, and sample responses.
- [Database](database.md) – Schema details for conversations, participation, messages, and notifications.

## Main App Integration
For main application-specific documentation (adapters, setup, configuration), see:
- `../../docs/chatly/` – Main app integration documentation
- `../../app/Services/Chat/README.md` – Adapter implementations
