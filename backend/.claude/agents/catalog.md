---
name: catalog-agent
description: Specialist for the Catalog module. Handles product/service catalog, items, types, and units management.
tools: Read, Edit, Write, Bash, Grep, Glob
model: sonnet
---

# Catalog Module Agent

> Inherits: [base.md](./base.md)

## Progress Tracker

| Task | Status | Notes |
|------|--------|-------|
| External contracts | 3/3 | Complete - CompanyInfoContract, MediaManagerContract, PermissionContract |
| Adapters | 3/3 | Complete - All in app/Services/Catalog/ |
| Tests | 1 | Needs more coverage |
| Vite config | Pending | Create catalog.vite.mjs |
| Frontend migration | Partial | Pages in both module and main app |

**Last updated**: Not yet started

---

## Domain

Product/service catalog management. Handles catalog items, types, and units for use in orders and offers.

## Scope

Only modify files within `modules/catalog/`

## Frontend Structure

```
resources/js/
├── Components/                # Catalog components
└── Pages/                     # Catalog pages
```

**Vite**: Inline alias `~Catalog` in main vite.config.mjs
**Needs**: Create `catalog.vite.mjs` config file

**Also in main app**: `resources/js/Pages/Catalog/` (needs migration)

## Key Models

| Model | Purpose |
|-------|---------|
| `CatalogItem` | Product or service in catalog |
| `CatalogItemType` | Category/type classification |
| `CatalogItemUnit` | Unit of measurement (pcs, hours, etc.) |

## Key Actions

| Action | Purpose |
|--------|---------|
| `CreateCatalogItem` | Add new catalog item |
| `UpdateCatalogItem` | Modify catalog item |
| `DeleteCatalogItem` | Remove catalog item |
| `CreateCatalogItemType` | Add new item type/category |

## External Contracts (Well-Defined)

Located in `src/Contracts/External/`:

| Contract | Purpose | Adapter Location |
|----------|---------|------------------|
| `CompanyInfoContract` | Company context | `app/Services/Catalog/CompanyInfoAdapter.php` |
| `MediaManagerContract` | Item images | `app/Services/Catalog/MediaManagerAdapter.php` |
| `PermissionContract` | Access control | `app/Services/Catalog/PermissionAdapter.php` |

## Exposed Contracts

Other modules need catalog data:

| Contract | Purpose |
|----------|---------|
| `CatalogItemLookupContract` | Allow orders/offers to search catalog |
| `CatalogPricingContract` | Get pricing for items |

## Module-Specific Patterns

### Action Concerns
Uses `Actions/Concerns/` for shared action logic (traits)

### Item Types Hierarchy
- Types can be nested/hierarchical
- Items belong to types

### Unit Management
- Standard units (pieces, hours, kg, etc.)
- Custom units per company

## Priority Tasks

1. Create Vite config file (`catalog.vite.mjs`)
2. Add more tests (currently only 1)
3. Define exposed contracts for order/offer modules

## Integration Points

| Module | Integration |
|--------|-------------|
| Orders | Uses catalog items for line items |
| Offers | Uses catalog items for offer items |
| Invoicify | Indirectly via orders |
