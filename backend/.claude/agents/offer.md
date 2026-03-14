---
name: offer-agent
description: Specialist for the Offer module. Handles quotes, PDF generation, status workflow, and order conversion.
tools: Read, Edit, Write, Bash, Grep, Glob
model: sonnet
---

# Offer Module Agent

> Inherits: [base.md](./base.md)

## Progress Tracker

| Task | Status | Notes |
|------|--------|-------|
| External contracts | 0/48 | Highest count - Need UserContract, CompanyContract, OrderContract, ClientContract, CatalogContract, MediaContract |
| Adapters | 0/? | None created yet |
| Tests | 0 | No tests exist |
| Vite config | Complete | offer.vite.mjs exists |
| Frontend migration | Partial | Also in resources/js/Pages/Order/Offer/ |

**Last updated**: Not yet started

---

## Domain

Quote/offer management. Handles creating offers for clients, PDF generation, status workflow (send, accept, reject), and conversion to orders.

## Scope

Only modify files within `modules/offer/`

## Frontend Structure

```
resources/js/
├── base.js                    # Base utilities
├── core.js                    # Entry point
├── Components/                # Offer components
└── Pages/
    └── (4 subdirectories)
```

**Vite**: Has `offer.vite.mjs` config file

**Also in main app**: `resources/js/Pages/Order/Offer/` (nested under Order, needs review)

## Key Models

| Model | Purpose |
|-------|---------|
| `Offer` | Main offer entity |
| `OfferItem` | Line items in offer |
| `OfferDeduction` | Discounts/deductions |
| `OfferHistory` | Status change history |
| `CompanyOfferConfiguration` | Per-company settings |

## Key Actions

| Action | Purpose |
|--------|---------|
| `CreateOffer` | Create new offer |
| `UpdateOffer` | Modify offer |
| `DeleteOffer` | Remove offer |
| `GenerateOfferPdf` | Generate PDF |
| `UpdateOfferMessages` | Update offer text/notes |

### Status Actions (`Actions/Status/`)

| Action | Purpose |
|--------|---------|
| `SendOffer` | Send to client |
| `AcceptOffer` | Client accepts |
| `RejectOffer` | Client rejects |
| `CancelOffer` | Cancel offer |

### Item Actions (`Actions/Item/`)

| Action | Purpose |
|--------|---------|
| `AttachItemToOffer` | Add catalog item |
| `AttachManualItem` | Add manual item |
| `DetachItemToOffer` | Remove item |
| `UpdateOfferItem` | Modify item |

### Company Actions (`Actions/Company/`)

| Action | Purpose |
|--------|---------|
| `SaveCompanyOfferConfiguration` | Save company settings |

## Service Providers

| Provider | Purpose |
|----------|---------|
| `OfferServiceProvider` | Main registration |
| `OfferEventServiceProvider` | Event listeners |

## Events & Listeners

| Event | Listeners |
|-------|-----------|
| `OfferAccepted` | `CreateOrderFromOffer`, `SendOfferAcceptedNotification` |
| `OfferRejected` | `SendOfferRejectedNotification` |
| Status changes | `LogOfferStatusChanged` |

## Notifications

| Notification | Purpose |
|--------------|---------|
| `OfferSentNotification` | Notify client of new offer |
| `OfferAcceptedNotification` | Notify team of acceptance |
| `OfferRejectedNotification` | Notify team of rejection |

## External Dependencies Needed

Currently has 48 `use App\` violations (highest). Needs contracts for:

| Dependency | Contract to Create |
|------------|-------------------|
| `App\Models\User` | `UserContract` |
| `App\Models\Company` | `CompanyContract` |
| `App\Models\Order` | `OrderContract` |
| `App\Models\Client` | `ClientContract` |
| Catalog items | `CatalogContract` |
| Media/PDF | `MediaManagerContract` |

## Exposed Contracts

Other modules may need:

| Contract | Purpose |
|----------|---------|
| `OfferLookupContract` | Query offers |
| `OfferToOrderContract` | Convert offer to order |

## Module-Specific Patterns

### Status Workflow
```
Draft → Sent → Accepted → (Creates Order)
              → Rejected
              → Cancelled
```

### Event-Driven
Heavy use of events for:
- Order creation on acceptance
- Notifications
- History logging

### Repository Pattern
`OfferRepository` for data access

### Transformers
- `OfferTransformer`
- `OfferItemTransformer`
- `CompanyOfferConfigurationTransformer`

## Priority Tasks

1. Define external contracts (48 violations - highest priority)
2. Add tests (currently 0)
3. Document status workflow

## Integration Points

| Module | Integration |
|--------|-------------|
| Catalog | Items from catalog |
| Orders | Converts to order on accept |
| Invoicify | Order generates invoice |
