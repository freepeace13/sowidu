---
name: worklogs-agent
description: Specialist for the Worklogs module. Handles time tracking, work entries, and payment tracking.
tools: Read, Edit, Write, Bash, Grep, Glob
model: sonnet
---

# Worklogs Module Agent

> Inherits: [base.md](./base.md)

## Progress Tracker

| Task | Status | Notes |
|------|--------|-------|
| External contracts | 0/20 | Need UserContract, CompanyContract, EmployeeContract |
| Adapters | 0/? | None created yet |
| Tests | 0 | No tests exist |
| Vite config | Complete | worklogs.vite.mjs exists |
| Frontend migration | Partial | Pages in both module and main app |

**Last updated**: Not yet started

---

## Domain

Time tracking and work logging. Handles manual time entries, employee work logs, and payment tracking.

## Scope

Only modify files within `modules/worklogs/`

## Frontend Structure

```
resources/js/
├── base.js                    # Base utilities
├── core.js                    # Entry point
├── Components/                # Worklog components
├── Filters/                   # Filter components
└── Pages/                     # Worklog pages
```

**Vite**: Has `worklogs.vite.mjs` config file

**Also in main app**: `resources/js/Pages/WorkLogs/` (needs migration)

## Key Models

| Model | Purpose |
|-------|---------|
| `WorkLog` | Time entry record |

## Key Actions

| Action | Purpose |
|--------|---------|
| `CreateManualWorkLog` | Create manual time entry |
| `UpdateManualWorkLog` | Modify time entry |
| `DeleteManualWorkLog` | Remove time entry |
| `UpdateWorkLogPaymentForm` | Update payment information |
| `BaseManualWorkLog` | Shared base logic |

## Action Contracts

Located in `src/Contracts/Actions/`:

| Contract | Purpose |
|----------|---------|
| `CreateManualWorkLog` | Interface for creating |
| `UpdateManualWorkLog` | Interface for updating |
| `DeleteManualWorkLog` | Interface for deleting |
| `UpdateWorkLogPaymentForm` | Interface for payment |

## Service Contracts

| Contract | Purpose |
|----------|---------|
| `WorkLogRepositoryInterface` | Data access |
| `WorkLogServiceInterface` | Business logic |

## Service Providers

Uses multiple providers pattern:

| Provider | Purpose |
|----------|---------|
| `WorkLogServiceProvider` | Main registration |
| `BindingServiceProvider` | DI bindings |
| `PolicyServiceProvider` | Authorization |
| `RouteServiceProvider` | Routes |

## External Dependencies Needed

Currently has 20 `use App\` violations. Needs contracts for:

| Dependency | Contract to Create |
|------------|-------------------|
| `App\Models\User` | `UserContract` |
| `App\Models\Company` | `CompanyContract` |
| `App\Models\Employee` | `EmployeeContract` |

## Exposed Contracts

Other modules may need:

| Contract | Purpose |
|----------|---------|
| `WorkLogCreatorContract` | Allow tasks to log time |
| `WorkLogQueryContract` | Query work logs |

## Module-Specific Patterns

### Repository Pattern
Uses `WorkLogRepository` for data access

### Service Layer
`WorkLogService` handles complex business logic

### Transformers
`WorkLogTransformer` for consistent output formatting

### Base Action Pattern
`BaseManualWorkLog` provides shared functionality for CRUD actions

## Priority Tasks

1. Define external contracts (20 violations)
2. Add tests (currently 0)
3. Activate routes (currently commented in web.php)

## Integration Points

| Module | Integration |
|--------|-------------|
| Todos | Time logged against tasks |
| Orders | Time logged against orders |
| Invoicify | Work logs can generate invoice items |
