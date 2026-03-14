# Documentation Organization Guidelines

This document defines how documentation should be organized across the Sowidu project.

## 📁 Directory Structure

```
sw-web/
├── docs/                           # Root documentation
│   ├── chatly/                     # Main app integration with Chatly
│   ├── offer/                      # Main app integration with Offer module  
│   └── DOCUMENTATION_GUIDELINES.md # This file
│
├── modules/
│   ├── chatly/
│   │   ├── docs/                   # Chatly module-specific docs
│   │   └── agents.md               # Agent patterns for Chatly
│   │
│   └── offer/
│       ├── docs/                   # Offer module-specific docs
│       └── agents.md               # Agent patterns for Offer
│
├── packages/
│   ├── MediaLibrary/
│   │   └── docs/                   # Package-specific docs
│   │
│   └── Urn/
│       └── docs/                   # Package-specific docs
│
└── app/
    └── Services/
        └── Chat/
            └── README.md           # Service-specific docs
```

## 🎯 Documentation Placement Rules

### Rule 1: Module-Specific Documentation

**Location**: `modules/{module-name}/docs/`

**What belongs here**:
- ✅ Module architecture and design patterns
- ✅ Module features and capabilities
- ✅ API documentation for module endpoints
- ✅ Module-specific testing guides
- ✅ Internal module workflows
- ✅ Module configuration options
- ✅ Module development guide

**Examples**:
- `modules/chatly/docs/architecture.md`
- `modules/chatly/docs/api.md`
- `modules/chatly/docs/testing-guide.md`

### Rule 2: Main App Integration Documentation

**Location**: `docs/{module-name}/`

**What belongs here**:
- ✅ Service provider registration
- ✅ Adapter implementations (in main app)
- ✅ Integration steps with main application
- ✅ Main app configuration for the module
- ✅ Setup and installation guides
- ✅ Troubleshooting integration issues

**Examples**:
- `docs/chatly/setup.md`
- `docs/chatly/adapters-implementation.md`
- `docs/chatly/README.md`

### Rule 3: Package Documentation

**Location**: `packages/{package-name}/docs/`

**What belongs here**:
- ✅ Package API documentation
- ✅ Package usage examples
- ✅ Package configuration
- ✅ Package testing guide

**Examples**:
- `packages/MediaLibrary/docs/usage.md`
- `packages/Urn/docs/api.md`

### Rule 4: Shared/Cross-Cutting Documentation

**Location**: `docs/`

**What belongs here**:
- ✅ Project-wide standards and conventions
- ✅ Overall architecture decisions
- ✅ Cross-module patterns
- ✅ General development guidelines
- ✅ Project setup and onboarding
- ✅ Deployment guides
- ✅ Contributing guidelines

**Examples**:
- `docs/directory-structure.md`
- `docs/DOCUMENTATION_GUIDELINES.md`
- `docs/CONTRIBUTING.md`

### Rule 5: Service/Component Documentation

**Location**: `app/{Service-Directory}/README.md`

**What belongs here**:
- ✅ Service-specific implementation details
- ✅ Adapter class documentation
- ✅ Helper utilities
- ✅ Service usage examples

**Examples**:
- `app/Services/Chat/README.md`
- `app/Repositories/README.md`

## 📝 Documentation Best Practices

### File Naming Conventions

Use **kebab-case** for documentation files:
- ✅ `integration-guide.md`
- ✅ `testing-guide.md`
- ✅ `external-contracts.md`
- ❌ `IntegrationGuide.md`
- ❌ `Testing_Guide.md`

### Index Files

Each `docs/` directory should have a `README.md` or `module-guide.md` that:
- Lists all documentation in the directory
- Provides quick navigation
- Links to related documentation
- Explains the scope of the docs

### Cross-Referencing

When referencing documentation from other locations:

```markdown
<!-- From module docs to main app docs -->
See [Setup Guide](../../docs/chatly/setup.md) for installation.

<!-- From main app docs to module docs -->
See [Module Architecture](../../modules/chatly/docs/architecture.md) for details.

<!-- Relative within same directory -->
See [API Documentation](api.md) for endpoint details.
```

### Table of Contents

For long documents (>200 lines), include a table of contents:

```markdown
## Table of Contents

- [Overview](#overview)
- [Installation](#installation)
- [Usage](#usage)
- [API Reference](#api-reference)
```

## 🤖 AI Agent Guidelines

When creating documentation, AI agents should:

1. **Determine Scope**
   - Is it module-specific? → `modules/{name}/docs/`
   - Is it integration/setup? → `docs/{name}/`
   - Is it cross-cutting? → `docs/`
   - Is it service-specific? → `app/{Service}/README.md`

2. **Check Existing Structure**
   - Look for existing `docs/` or `README.md` files
   - Follow established patterns in the directory
   - Update index files when adding new docs

3. **Cross-Reference**
   - Link to related documentation
   - Update index files
   - Maintain bidirectional links when appropriate

4. **Use Templates**
   - Follow existing documentation format
   - Use consistent headers and structure
   - Include examples and code snippets

## 📋 Documentation Checklist

Before creating new documentation:

- [ ] Determined correct location based on scope
- [ ] Checked for existing related documentation
- [ ] Used kebab-case for filename
- [ ] Included clear title and purpose
- [ ] Added table of contents (if >200 lines)
- [ ] Cross-referenced related docs
- [ ] Updated index/README files
- [ ] Included code examples where appropriate
- [ ] Added to version control

## 🔍 Examples by Scenario

### Scenario 1: New Module Feature

**Question**: Where do I document a new feature in the Chatly module?

**Answer**: `modules/chatly/docs/features.md`

**Also Update**:
- `modules/chatly/docs/module-guide.md` (add link)
- `modules/chatly/README.md` (if it's a major feature)

### Scenario 2: Main App Service Provider

**Question**: Where do I document how to register a new service provider?

**Answer**: `docs/{module-name}/setup.md`

**Example**: `docs/chatly/setup.md`

### Scenario 3: Cross-Module Pattern

**Question**: Where do I document a pattern that multiple modules use?

**Answer**: `docs/patterns/{pattern-name}.md`

**Example**: `docs/patterns/external-contracts.md`

### Scenario 4: Package Usage

**Question**: Where do I document how to use a package?

**Answer**: `packages/{package-name}/docs/usage.md`

**Also Consider**:
- `packages/{package-name}/README.md` for quick start

### Scenario 5: Testing Strategy

**Module-Specific Tests**: `modules/{name}/docs/testing-guide.md`

**Project-Wide Testing**: `docs/testing/README.md`

**Integration Tests**: `docs/{module}/integration-tests.md`

## 🚀 Migration Guide

If documentation is in the wrong location:

1. **Move the file** to the correct location
2. **Update all references** in other docs
3. **Update index files** in both old and new locations
4. **Create redirects** if necessary (add note in old location)
5. **Update README files** to reflect new structure

## 📚 Resources

- [Markdown Guide](https://www.markdownguide.org/)
- [Documentation Best Practices](https://www.writethedocs.org/guide/)
- [Divio Documentation System](https://documentation.divio.com/)

---

**Note**: This guideline applies to all future documentation. When in doubt, follow the scope-based placement rules above.

**Last Updated**: Based on Chatly refactoring (2024)

