# Catalog Module

> Agent: [.claude/agents/catalog.md](/.claude/agents/catalog.md)

Product/service catalog management with items, types, and units.

## Progress Tracker

| Task | Status | Notes |
|------|--------|-------|
| External contracts | 3/3 | Complete |
| Adapters | 3/3 | All in app/Services/Catalog/ |
| Tests | 1 | Needs more |
| Vite config | Pending | Create catalog.vite.mjs |
| Frontend | Partial | Some pages in main app |

**Last updated**: 2025-01-15 - Initial tracking

## Status: Partial

- [x] Service provider
- [x] External contracts defined
- [x] Routes in module
- [ ] Complete test coverage

## Structure

```
catalog/src/
├── CatalogServiceProvider.php
├── Actions/
│   ├── CreateCatalogItem.php
│   ├── UpdateCatalogItem.php
│   ├── DeleteCatalogItem.php
│   └── Concerns/ValidatesCatalogItems.php
├── Contracts/
│   ├── CreatesCatalogItem.php
│   ├── UpdatesCatalogItem.php
│   ├── DeletesCatalogItem.php
│   └── External/
│       ├── PermissionContract.php
│       ├── CompanyInfoContract.php
│       └── MediaManagerContract.php
├── Models/
│   ├── CatalogItem.php
│   ├── CatalogItemType.php
│   └── CatalogItemUnit.php
├── Policies/CatalogItemPolicy.php
├── Repositories/CatalogItemRepository.php
├── Services/CatalogService.php
└── Transformers/CatalogItemTransformer.php
```

## External Contracts

| Contract | Purpose | Adapter Location |
|----------|---------|------------------|
| `PermissionContract` | Authorization checks | `app/Services/Catalog/` |
| `CompanyInfoContract` | Team/company context | `app/Services/Catalog/` |
| `MediaManagerContract` | File attachments | `app/Services/Catalog/` |

## Key Patterns

### Validation Concern
Shared validation via trait:
```php
use Modules\Catalog\Actions\Concerns\ValidatesCatalogItems;
```

### Models
- `CatalogItem` - Main catalog entry (products, services)
- `CatalogItemType` - Categories/classifications
- `CatalogItemUnit` - Measurement units (pcs, hrs, kg)

## Completion Checklist

- [x] Service provider registered
- [x] Routes in module
- [x] External contracts defined (Permission, CompanyInfo, MediaManager)
- [x] Vite alias (inline `~Catalog` in vite.config.mjs)
- [ ] Vite config file (`catalog.vite.mjs`) - needs creation
- [ ] Complete test coverage
- [ ] Vue/Inertia pages in module (check if still in main app)
- [ ] Adapters fully implemented in `app/Services/Catalog/`

## AI Guidelines

When working here:
1. Use external contracts for main app dependencies
2. Follow `ValidatesCatalogItems` pattern for validation
3. Use `CatalogItemTransformer` for API responses
4. Check `CatalogItemPolicy` for authorization
5. **Priority**: Create `catalog.vite.mjs` and add tests
