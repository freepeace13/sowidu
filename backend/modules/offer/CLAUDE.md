# Offer Module

> Agent: [.claude/agents/offer.md](/.claude/agents/offer.md)

Quote/proposal management with pricing, status workflows, and client delivery.

## Progress Tracker

| Task | Status | Notes |
|------|--------|-------|
| External contracts | 0/48 | Highest count - 48 `use App\` violations |
| Adapters | 0/? | None created |
| Tests | Some | Has tests |
| Vite config | Complete | offer.vite.mjs exists |
| Frontend | Partial | Also in resources/js/Pages/Order/Offer/ |

**Last updated**: 2025-01-15 - Initial tracking

## Status: Partial (was marked Complete but has contract violations)

- [x] Service provider
- [x] Routes self-contained
- [x] Event-driven architecture
- [x] Test coverage

## Structure

```
offer/src/
├── OfferServiceProvider.php
├── OfferEventServiceProvider.php    # Separate event provider
├── OfferService.php                 # Core service
├── Actions/
│   └── (multiple action subdirectories)
├── Console/                         # Artisan commands
├── Controllers/                     # Note: directly in src/
├── Events/
├── Listeners/
├── Mail/
├── Middleware/
├── Models/
│   └── Relations/
├── Notifications/
├── Policies/
├── Repositories/
├── Support/
└── Transformers/
```

## Key Patterns

### Two Service Providers
- `OfferServiceProvider` - Main registration
- `OfferEventServiceProvider` - Event/listener bindings

### Event-Driven
Rich event system with listeners:
```
Events/
├── OfferCreated.php
├── OfferAccepted.php
├── OfferRejected.php
└── ...

Listeners/
├── SendOfferNotification.php
└── ...
```

### Controllers Location
Note: Controllers are directly in `src/Controllers/`, not `src/Http/Controllers/`.

### Mail & Notifications
Full notification support:
```
Mail/
└── OfferMail.php

Notifications/
└── OfferNotification.php
```

## Models

| Model | Purpose |
|-------|---------|
| `Offer` | Main offer/quote entity |
| `OfferItem` | Line items |
| + Relations | Model relationships |

## Integration Points

This module integrates with:
- **Orders** - Offers can become orders
- **Invoicify** - Offers can generate invoices
- **Account** - Offer settings in `routes/account.php`

## Completion Checklist

- [x] Service provider registered
- [x] OfferEventServiceProvider for events
- [x] Routes self-contained in module
- [x] Vite config (`offer.vite.mjs`)
- [x] Mail templates
- [x] Notifications
- [x] Tests
- [x] Policies
- [x] Repositories
- [x] Transformers
- [ ] **External contracts** - some main app models used directly:
  - [ ] May need UserContract
  - [ ] May need CompanyContract
  - [ ] Check for `App\Models\*` imports

## AI Guidelines

When working here:
1. This module is COMPLETE - use as reference
2. Follow the event-driven pattern for state changes
3. Use separate event provider for new events
4. Check Mail/ and Notifications/ for client communication
5. See `agents.md` for orchestration patterns
6. **Note**: Consider adding external contracts for full isolation

## Related Files
- `agents.md` - Agent orchestration documentation
