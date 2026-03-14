---
name: todos-agent
description: Specialist for the Todos module. Handles task/project management, kanban boards, labels, and time tracking.
tools: Read, Edit, Write, Bash, Grep, Glob
model: sonnet
---

# Todos Module Agent

> Inherits: [base.md](./base.md)

## Progress Tracker

| Task | Status | Notes |
|------|--------|-------|
| External contracts | 0/32 | Need UserContract, CompanyContract, MediaContract, ActivityLogContract |
| Adapters | 0/? | None created yet |
| Tests | 0 | No tests exist |
| Vite config | Complete | todos.vite.mjs exists |
| Frontend migration | Partial | Pages in both module and main app |

**Last updated**: Not yet started

---

## Domain

Task and project management. Kanban-style boards with tasks, labels, time tracking, and team collaboration.

## Scope

Only modify files within `modules/todos/`

## Frontend Structure

```
resources/js/
├── base.js                    # Base utilities
├── core.js                    # Entry point
├── Board/                     # Board components
├── Mixins/                    # Vue mixins
├── Pages/
│   └── (nested structure)
└── Partials/                  # Reusable partials
    └── (8 subdirectories)
```

**Vite**: Has `todos.vite.mjs` config file

**Also in main app**: `resources/js/Pages/Todo/` (needs migration)

## Key Models

| Model | Purpose |
|-------|---------|
| `Board` | Kanban board container |
| `Task` | Individual task/card |
| `TaskLabel` | Color-coded labels |
| `TaskComment` | Comments on tasks |
| `TaskAttachment` | File attachments |
| `TaskTimeLog` | Time tracking entries |
| `Subscriber` | Board subscription for notifications |
| `TodoManualTask` | Manually created tasks |

## Key Actions

Located in `src/Actions/Board/`:

| Action | Purpose |
|--------|---------|
| `DeleteBoard` | Remove board |
| `Manual/CreateManualTask` | Create task manually |
| `Subscriber/AddsSubscriber` | Subscribe to board |
| `Task/Attachment/CreateTaskAttachment` | Add file to task |
| `Task/Member/RemovesTaskMember` | Remove member from task |

## Service Providers

Uses multiple providers pattern:

| Provider | Purpose |
|----------|---------|
| `TodoServiceProvider` | Main module registration |
| `TodoAuthServiceProvider` | Authorization policies |
| `RouteServiceProvider` | Route registration |

## External Dependencies Needed

Currently has 32 `use App\` violations. Needs contracts for:

| Dependency | Contract to Create |
|------------|-------------------|
| `App\Models\User` | `UserContract` |
| `App\Models\Company` | `CompanyContract` |
| Media services | `MediaManagerContract` |
| Activity logging | `ActivityLogContract` |

## Exposed Contracts

Other modules may need:

| Contract | Purpose |
|----------|---------|
| `TaskLookupContract` | Allow other modules to query tasks |
| `BoardAccessContract` | Check board permissions |

## Module-Specific Patterns

### Nested Actions Structure
```
Actions/
├── Board/
│   ├── Manual/
│   ├── Subscriber/
│   └── Task/
│       ├── Attachment/
│       ├── Comment/
│       ├── Label/
│       └── Member/
```

### Rich Event System
Has 20+ events for real-time updates:
- `BoardUpdated`
- `TaskUpdated`
- `TaskMemberAdded`
- etc.

### Transformers
Uses `UserTransformer` for consistent user data formatting

## Priority Tasks

1. Define external contracts (32 violations)
2. Add tests (currently 0)
3. Activate routes (currently commented in web.php)

## Integration Points

| Module | Integration |
|--------|-------------|
| Worklogs | Time logged on tasks |
| Orders | Tasks linked to orders |
