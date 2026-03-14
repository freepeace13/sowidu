# Claude Code Setup Guide

This project is configured to work with [Claude Code](https://claude.ai/claude-code), Anthropic's official CLI for AI-assisted development.

## Quick Start

1. Install Claude Code: `npm install -g @anthropic-ai/claude-code`
2. Navigate to project: `cd /path/to/sowidu`
3. Start Claude: `claude`

## Configuration Structure

```
sowidu/
├── CLAUDE.md                    # Lean (~150 lines): Critical rules, command references
├── CLAUDE-README.md             # This guide
├── .claude/
│   ├── commands/                # Slash command definitions
│   │   ├── module-*.md          # Modularization commands
│   │   ├── code-audit.md        # Code quality commands
│   │   └── ...
│   ├── context/                 # Detailed context (loaded on-demand)
│   │   ├── architecture.md      # Module structure, contracts, Vite
│   │   ├── code-quality.md      # SOLID, design patterns, smells
│   │   ├── security.md          # Auth, validation, XSS, SQL injection
│   │   ├── testing.md           # Coverage, test patterns
│   │   ├── database.md          # Migrations, indexes, queries
│   │   ├── api.md               # Response formats, status codes
│   │   ├── git.md               # Branches, commits, PR checklist
│   │   └── status.md            # Current module status
│   └── agents/                  # Agent specifications
└── modules/*/CLAUDE.md          # Module-specific context (auto-loaded)
```

## Context-Savvy Loading

The configuration is split to load only what's needed:

| What | When Loaded | Size |
|------|-------------|------|
| `CLAUDE.md` | Always | ~150 lines |
| `.claude/context/*.md` | On command | ~100-200 lines each |
| `modules/*/CLAUDE.md` | When in directory | Module-specific |

### Loading Context On-Demand

```bash
> /context-architecture    # Load module structure, contracts, Vite
> /context-quality         # Load SOLID, patterns, smells
> /context-security        # Load auth, validation, XSS
> /context-testing         # Load coverage, test patterns
> /context-database        # Load migrations, indexes
> /context-api             # Load response formats
> /context-git             # Load branches, commits, PR checklist
```

## Slash Commands

### Modularization Commands

| Command | Description |
|---------|-------------|
| `/module-guide` | Show modularization patterns and architecture |
| `/module-create` | Scaffold a new module with proper structure |
| `/module-refactor` | Move code from main app to a module |
| `/module-status` | Check current modularization progress |

### Code Quality Commands

| Command | Description |
|---------|-------------|
| `/code-audit` | Full codebase audit: duplicates, unused code, violations |
| `/find-duplicates` | Find duplicated code blocks across modules |
| `/find-unused` | Find unused classes, methods, imports, variables |
| `/find-violations` | Find architecture violations (App\ imports in modules) |
| `/security-scan` | Scan for security vulnerabilities |

### Documentation Commands

| Command | Description |
|---------|-------------|
| `/generate-user-guide` | Generate user documentation for a feature/module |
| `/generate-tutorial` | Create step-by-step tutorial with screenshots |
| `/translate-docs {lang}` | Translate documentation (de, nl, fr, es, it) |
| `/generate-api-docs` | Generate API documentation from code |

## Key Conventions

### Module Architecture

This project uses **Ports & Adapters (Hexagonal) Architecture**:

- **Modules** are self-contained in `modules/{name}/`
- **External Contracts** define what a module needs from the outside world
- **Adapters** in main app implement those contracts

### Critical Rule

Modules must NEVER import directly from `App\` namespace:

```php
// BAD
use App\Models\User;

// GOOD
use Modules\Chatly\Contracts\External\UserSearchContract;
```

### SOLID Principles

All code MUST follow SOLID principles (load `/context-quality` for details):

| Principle | Summary |
|-----------|---------|
| **S**ingle Responsibility | One class, one reason to change |
| **O**pen/Closed | Open for extension, closed for modification |
| **L**iskov Substitution | Subtypes must be substitutable |
| **I**nterface Segregation | Many specific interfaces > one fat interface |
| **D**ependency Inversion | Depend on abstractions, not concretions |

### Accepted Design Patterns

| Pattern | Use Case |
|---------|----------|
| Action | Business logic (CreateInvoiceAction) |
| Repository | Data access encapsulation |
| DTO | Type-safe data transfer (Spatie Data) |
| Factory | Complex object creation |
| Strategy | Interchangeable algorithms |
| Observer | Events and listeners |
| Adapter | External contract implementations |

### Reference Module

Use `modules/invoicify` as the reference for creating new modules.

## Example Workflows

### Code Quality Audit

```bash
> /code-audit
# Review the report, fix Critical → Warnings → Info

> /find-violations
# For each violation:
# 1. Create external contract in module
# 2. Create adapter in app/Services/
# 3. Register binding in service provider
```

### Creating a New Module

```bash
> /module-create payments
# Follow the generated structure
# Define external contracts for dependencies
```

### Working on Security

```bash
> /context-security
# Now Claude has full security context
# Work on auth, validation, etc.
```

### Working on Tests

```bash
> /context-testing
# Now Claude has full testing context
# Write unit/feature tests with proper patterns
```

## Vite Setup for Modules

Modules with frontend assets need Vite configuration.

### Existing Module Vite Configs

| Module | Config File | Alias |
|--------|-------------|-------|
| offer | `offer.vite.mjs` | `@Offer` |
| todos | `todos.vite.mjs` | `@Todos` |
| worklogs | `worklogs.vite.mjs` | `@Worklogs` |
| invoicify | (in vite.config.mjs) | `~Invoicify` |
| chatly | (in vite.config.mjs) | `~Chatly` |
| catalog | (in vite.config.mjs) | `~Catalog` |

Load `/context-architecture` for full Vite setup details.

## Files Reference

| File | Purpose |
|------|---------|
| `CLAUDE.md` | Critical rules, command references (~150 lines) |
| `CLAUDE-README.md` | This guide for developers |
| `.claude/commands/*.md` | Slash command definitions |
| `.claude/context/*.md` | Detailed context (on-demand) |
| `modules/*/CLAUDE.md` | Module-specific instructions |

## Benefits of This Structure

| Before | After |
|--------|-------|
| 1500+ lines loaded every time | ~150 lines base |
| All patterns always loaded | Patterns loaded on `/context-quality` |
| All security docs always loaded | Security loaded on `/context-security` |
| Status always loaded | Status loaded on `/module-status` |
| Same context everywhere | Module-specific context auto-loads |

## Troubleshooting

### Commands not showing up
- Ensure `.claude/commands/` folder exists
- Check file extensions are `.md`
- Restart Claude Code

### Context not loading
- Use `/context-{name}` to load specific context
- Check `.claude/context/` folder exists
- Ensure files are valid markdown
