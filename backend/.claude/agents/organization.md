---
name: organization-agent
description: Specialist for the Organization module (candidate). Handles companies, employees, roles, and permissions.
tools: Read, Edit, Write, Bash, Grep, Glob
model: sonnet
---

# Organization Module Agent (Candidate)

> Inherits: [base.md](./base.md)

## Progress Tracker

| Task | Status | Notes |
|------|--------|-------|
| Module created | No | Need to create modules/organization/ |
| Service provider | No | Need OrganizationServiceProvider |
| External contracts | 0/? | Need UserContract |
| Exposed contracts | 0/? | CompanyContract, EmployeeContract, PermissionContract - ALL modules need these |
| Models migrated | 0/6 | Company, Employee, Role, Permission, Team, etc. |
| Actions migrated | 0/16 | |
| Controllers migrated | 0/12 | |
| Routes migrated | No | Partial in routes/account.php |
| Tests | 0 | No tests |
| Vite config | No | Need organization.vite.mjs |
| Frontend migrated | 0/? | Split across Account + AppSettings |

**Last updated**: Not yet started

---

## Status: NOT YET MODULARIZED

This is a candidate for modularization. Code currently lives in main app.

## Domain

Company/organization management. Handles companies, employees, roles, permissions, and organization settings.

## Frontend (to migrate)

Currently spread across:

```
resources/js/Pages/
├── Account/                   # Account settings
│   └── (4 subdirectories)
└── AppSettings/               # App/org settings
    └── (6 subdirectories)
```

**Note**: Organization frontend is split between Account and AppSettings. Will need consolidation during migration.

## Current Location

| Component | Location | Count |
|-----------|----------|-------|
| Inertia Controllers | `app/Http/Controllers/Inertia/Organization/` | 12 |
| Actions | `app/Actions/Organization/` | 16 |
| Models | `app/Models/` | 6 |
| Events | `app/Events/Organization/` | 4 |
| Routes | `routes/account.php` (partial) | 1 |

## Key Actions (to migrate)

| Action | Purpose |
|--------|---------|
| `UpdateOrganizationProfile` | Update company profile |
| `CreateRole` | Create permission role |
| `UpdateRole` | Modify role |
| `AssignFounderPermissions` | Set founder permissions |

### Subdirectories
- `Employee/` - Employee management
- `Role/` - Role/permission management
- `Settings/` - Organization settings

## Key Models (to migrate)

| Model | Purpose |
|-------|---------|
| `Company` | Organization entity |
| `Employee` | Employee/team member |
| `Role` | Permission role |
| `Permission` | Individual permission |
| `Team` | Team grouping |

## Target Module Structure

```
modules/organization/
├── src/
│   ├── OrganizationServiceProvider.php
│   ├── Actions/
│   │   ├── Employee/
│   │   ├── Role/
│   │   └── Settings/
│   ├── Contracts/
│   │   ├── External/
│   │   │   └── UserContract.php
│   │   └── Exposed/
│   │       ├── CompanyContract.php
│   │       ├── EmployeeContract.php
│   │       ├── PermissionContract.php
│   │       └── TeamContract.php
│   ├── Models/
│   │   ├── Company.php
│   │   ├── Employee.php
│   │   ├── Role.php
│   │   └── Permission.php
│   └── Http/Controllers/
├── routes/web.php
└── tests/
```

## External Contracts Needed

| Dependency | Contract |
|------------|----------|
| User | `UserContract` |

## Exposed Contracts

Critical - almost all modules need organization context:

| Contract | Consumers |
|----------|-----------|
| `CompanyContract` | ALL modules |
| `EmployeeContract` | Worklogs, Todos, Orders |
| `PermissionContract` | ALL modules for access control |
| `TeamContract` | Todos, Orders |

## Migration Priority

**MEDIUM** - Core dependency but tightly coupled to auth system.

## Migration Considerations

This module is special because:
- `Company` and `Employee` are used everywhere
- Permission system is foundational
- May need to stay partially in main app

### Possible Approach

1. Keep `Company` model in main app initially
2. Create exposed contracts for organization data
3. Other modules use contracts instead of direct model access
4. Gradually migrate as contracts are adopted

## Integration Points

| Module | Integration |
|--------|-------------|
| ALL | Company context |
| ALL | Permission checks |
| Worklogs | Employee lookup |
| Todos | Team/member access |
| Orders | Company settings |
| Invoicify | Company details on invoice |

## Notes

- This is foundational infrastructure
- Exposed contracts are more important than modularization
- Consider creating `CompanyContract` first before full migration
- Permission system integration needs careful planning
