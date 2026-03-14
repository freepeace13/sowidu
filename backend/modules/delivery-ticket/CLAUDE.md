# Delivery-Ticket Module

> Agent: [.claude/agents/deliveryticket.md](/.claude/agents/deliveryticket.md)

Delivery ticket management for tracking materials, documents, and deliveries.

## Progress Tracker

| Task | Status | Notes |
|------|--------|-------|
| External contracts | 12/12 | Complete - all contracts defined |
| Adapters | 12/12 | All in app/Services/DeliveryTicket/ |
| Code smells | 12 | Remaining `use App\` in controllers (transformers, enums) |
| Tests | 26 | Unit tests for all actions - ALL PASSING |
| Vite config | Complete | delivery-ticket.vite.mjs exists |
| Frontend | Complete | All in module |
| Type safety | Complete | All files have strict_types |
| Service Provider | Complete | Registered in config/app.php |

**Last updated**: 2026-01-20 - Fixed CompanyAdapter getCurrency return type

## Status: Mostly Complete

- [x] Service provider (multiple providers pattern)
- [x] Action contracts use generic types (Authenticatable, Model)
- [x] Service contracts use generic types
- [x] Type safety (declare strict_types)
- [x] External contracts (12 contracts)
- [x] Adapters in main app (12 adapters)
- [x] Service providers registered
- [x] Routes in module
- [x] Actions use dependency injection with external contracts
- [x] Services use dependency injection with external contracts
- [x] Test coverage (26 unit tests - ALL PASSING)
- [ ] Controller transformers (complex method chaining - future iteration)

## Structure

```
delivery-ticket/src/
├── Providers/
│   ├── DeliveryTicketServiceProvider.php   # Main provider
│   ├── BindingServiceProvider.php          # Container bindings
│   ├── PolicyServiceProvider.php           # Policies
│   └── RouteServiceProvider.php            # Routes
├── Actions/
│   ├── CreateDeliveryTicket.php
│   ├── UpdateDeliveryTicket.php
│   ├── DeleteDeliveryTicket.php
│   ├── AddMaterialToDeliveryTicket.php
│   ├── UpdateDeliveryTicketMaterial.php
│   ├── RemoveMaterialOnDeliveryTicket.php
│   ├── AddDocumentToDeliveryTicket.php
│   ├── RemoveDocumentToDeliveryTicket.php
│   ├── UpdateDeliveryAddressTicket.php
│   └── UpdateDelivererTicket.php
├── Contracts/
│   ├── Actions/                    # 10 action contracts
│   ├── Services/                   # 2 service contracts
│   └── External/                   # 12 external contracts
├── Events/
│   ├── DeliveryTicketMaterialsAdded.php
│   └── DeliveryTicketMaterialsUpdated.php
├── Http/
│   ├── Controllers/
│   │   ├── Inertia/
│   │   │   ├── DeliveryTicketController.php
│   │   │   ├── DeliveryTicketMaterialController.php
│   │   │   └── DeliveryTicketDocumentController.php
│   │   └── Json/
│   │       ├── GetDeliveryTicketController.php
│   │       ├── ShowDeliveryTicketController.php
│   │       └── ShowDeliveryTicketMaterialsController.php
│   └── Middleware/
│       └── DeliveryTicketsInertiaHandler.php
├── Models/
│   ├── DeliveryTicket.php
│   ├── DeliveryTicketMaterial.php
│   └── DeliveryTicketDocument.php
├── Policies/
│   └── DeliveryTicketPolicy.php
└── Services/
    ├── DeliveryTicketsService.php
    └── DeliveryTicketMaterialService.php
```

## Models

| Model | Purpose |
|-------|---------|
| `DeliveryTicket` | Main delivery ticket record |
| `DeliveryTicketMaterial` | Materials attached to ticket |
| `DeliveryTicketDocument` | Documents attached to ticket |

## Remaining Violations (12 - Controllers Only)

The core business logic (Actions, Services, Contracts) has been refactored to use external contracts.
The remaining violations are in Controllers and Models for UI/display concerns:

### Controllers - UI Dependencies
- `App\Enums\DeliveryTicketType` - Enum for dropdown options
- `App\Http\Controllers\Controller` - Base controller (framework dependency)
- `App\Models\CatalogItemUnit` - For unit options dropdown
- `App\Support\Vuetify\CreateOptions` - UI helper for dropdowns
- `App\Traits\InteractsWithImpersonator` - Impersonation trait
- `App\Transformers\DeliveryTicketTransformer` - Complex method chaining
- `App\Transformers\DeliveryTicketMaterialTransformer` - Complex method chaining
- `App\Transformers\MediaTransformer` - Complex method chaining

### Models - Relationship Traits (Acceptable)
- `App\Models\Relations\AuthoredByUser`
- `App\Models\Relations\CompanyOwned`
- `App\Models\Relations\CanBeInvoiceItem`
- `App\Models\Relations\Searchable`

### Fixed Violations (Now Using Contracts)
- ~~`App\Models\User`~~ → `Authenticatable`
- ~~`App\Models\Company`~~ → `Model`
- ~~`App\Models\Addressbook`~~ → `ModelConfigContract`
- ~~`App\Models\CatalogItem`~~ → `CatalogContract`
- ~~`App\Models\Order`~~ → `ModelConfigContract`
- ~~`App\Services\CacheService`~~ → `CacheContract`
- ~~`App\Rules\OwnedByCompany`~~ → `ValidationContract`

## External Contracts (Complete)

Located in `src/Contracts/External/`:

| Contract | Adapter | Purpose |
|----------|---------|---------|
| `ModelConfigContract` | `ModelConfigAdapter` | Eloquent model classes |
| `TransformerContract` | `TransformerAdapter` | External transformations |
| `CatalogContract` | `CatalogAdapter` | Catalog items and units |
| `MediaContract` | `MediaAdapter` | Document/media operations |
| `CompanyContract` | `CompanyAdapter` | Company operations |
| `AuthorizationContract` | `AuthorizationAdapter` | Team authorization |
| `ImpersonatorContract` | `ImpersonatorAdapter` | Impersonation context |
| `CacheContract` | `CacheAdapter` | Caching operations |
| `InvoiceContract` | `InvoiceAdapter` | Invoice relationships |
| `VuetifyContract` | `VuetifyAdapter` | CreateOptions helper |
| `ValidationContract` | `ValidationAdapter` | OwnedByCompany rule |
| `InertiaContract` | `InertiaAdapter` | Inertia shared data |

Bindings registered in `app/Providers/DeliveryTicketServiceProvider.php`.

## AI Guidelines

When working here:
1. Follow the multiple providers pattern
2. Create action contract before implementation
3. Use external contracts for all main app dependencies
4. Check `DeliveryTicketPolicy` for authorization
5. Use `Modules\Shared\Enums\Permissions` for permission constants
6. Inject external contracts via constructor

## Migration Steps

1. Create external contracts in `src/Contracts/External/`

2. Create adapters in `app/Services/DeliveryTicket/`

3. Bind contracts to adapters in `app/Providers/DeliveryTicketServiceProvider.php`

4. Update all files to use contracts instead of `use App\*`

5. Move enums to module (`DeliveryTicketType`) or use shared

6. Move transformers to module

7. Add test coverage
