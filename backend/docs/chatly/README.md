# Chatly Integration Documentation

This directory contains documentation for integrating the Chatly module with the main Sowidu application.

## 📚 Documentation Index

### Setup & Installation
- **[Setup Guide](setup.md)** - Complete setup and verification instructions
- **[Adapters Implementation](adapters-implementation.md)** - Details about adapter classes in `app/Services/Chat/`

### Architecture & Integration
- **[Refactoring Summary](refactoring-summary.md)** - Complete overview of the Chatly refactoring project
- **Adapter Classes**: See `app/Services/Chat/README.md` for adapter-specific documentation

## 🔗 Related Documentation

### Module Documentation
For Chatly module-specific documentation, see:
- `modules/chatly/docs/` - Module architecture, features, and API
- `modules/chatly/README.md` - Module overview

### Main App Documentation
- `docs/directory-structure.md` - Overall project structure
- `app/Services/Chat/README.md` - Chat service adapters

## 📖 Quick Links

| Topic | Document | Location |
|-------|----------|----------|
| **Setup** | Setup Guide | `docs/chatly/setup.md` |
| **Adapters** | Implementation Guide | `docs/chatly/adapters-implementation.md` |
| **Integration** | Integration Guide | `modules/chatly/docs/integration-guide.md` |
| **Architecture** | Architecture Overview | `modules/chatly/docs/architecture.md` |
| **Testing** | Testing Guide | `modules/chatly/docs/testing-guide.md` |
| **External Contracts** | Contract Specs | `modules/chatly/docs/external-contracts.md` |

## 🎯 Purpose

These documents explain how the **main application** integrates with the **Chatly module** through:

1. **Service Provider** (`app/Providers/ChatServiceProvider.php`)
2. **Adapter Classes** (`app/Services/Chat/*Adapter.php`)
3. **Contract Implementations** (implementing interfaces from `modules/chatly/src/Contracts/External/`)

## 📋 Documentation Scope

### This Directory (`docs/chatly/`)
- Main application setup and configuration
- Adapter implementation details
- Integration with existing app infrastructure
- Service provider registration

### Module Directory (`modules/chatly/docs/`)
- Chatly module architecture
- Module features and capabilities
- API documentation
- Module-specific testing

## 🔄 Documentation Guidelines

When adding new documentation:

1. **Main App Integration** → Place in `docs/chatly/`
2. **Module Features** → Place in `modules/chatly/docs/`
3. **Cross-Cutting Concerns** → Reference from both locations

See `modules/chatly/agents.md` for complete documentation organization guidelines.

