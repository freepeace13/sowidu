---
name: orders-agent
description: Specialist for the Orders module (candidate). Handles order management, products, work logs, and time tracking.
tools: Read, Edit, Write, Bash, Grep, Glob
model: sonnet
---

# Orders Module Agent (Candidate)

> Inherits: [base.md](./base.md)

## Progress Tracker

| Task | Status | Notes |
|------|--------|-------|
| Module created | No | Need to create modules/orders/ |
| Service provider | No | Need OrdersServiceProvider |
| External contracts | 0/? | Need UserContract, CompanyContract, AddressbookContract, CatalogContract, MediaContract |
| Exposed contracts | 0/? | OrderLookupContract, OrderCreatorContract for other modules |
| Models migrated | 0/3 | Order, OrderProduct, OrderAttachment |
| Actions migrated | 0/32 | Largest action set |
| Controllers migrated | 0/17 | 14 Inertia + 3 Json |
| Routes migrated | No | routes/order.php |
| Tests | 0 | No tests |
| Vite config | No | Need orders.vite.mjs |
| Frontend migrated | 0/? | Largest frontend - 13 subdirs |

**Last updated**: Not yet started

---

## Status: NOT YET MODULARIZED

This is a candidate for modularization. Code currently lives in main app.

## Domain

Order management. The largest domain - handles incoming/outgoing orders, products, work logs, time tracking, and client communication.

## Frontend (to migrate)

Currently in `resources/js/Pages/Order/`:

```
Order/
├── Components/                # Order-specific components
├── DeliveryTicket/            # Delivery ticket pages (move to deliveryticket module)
├── Files/                     # File management
├── Incoming/                  # Incoming orders
├── Invoices/                  # Invoice views (move to invoicify module)
├── Mixins/                    # Vue mixins
├── New/                       # Order creation
├── Offer/                     # Offer views (move to offer module)
├── Outgoing/                  # Outgoing orders
├── Products/                  # Product management
├── TimeLogs/                  # Time log views
├── IncomingOrders.vue
├── OutgoingOrders.vue
├── Overview.vue
├── OrderLayout.vue
└── Show.vue                   # Main order view (19KB - large file)
```

**Note**: Some nested folders belong to other modules:
- `DeliveryTicket/` → deliveryticket module
- `Invoices/` → invoicify module
- `Offer/` → offer module

## Current Location

| Component | Location | Count |
|-----------|----------|-------|
| Inertia Controllers | `app/Http/Controllers/Inertia/Order/` | 14 |
| Json Controllers | `app/Http/Controllers/Json/Order/` | 3 |
| Actions | `app/Actions/Order/` | 32 |
| Models | `app/Models/Order*.php` | 3 |
| Events | `app/Events/Order/` | 11 |
| Listeners | `app/Listeners/Order/` | 6 |
| Routes | `routes/order.php` | 1 |

## Key Models (to migrate)

| Model | Purpose |
|-------|---------|
| `Order` | Main order entity |
| `OrderProduct` | Line items/products |
| `OrderAttachment` | File attachments |

## Key Actions (to migrate)

### Core Actions
| Action | Purpose |
|--------|---------|
| `UpdateOrder` | Modify order |
| `GetOrderSummaries` | Order summaries |

### Work Log Actions
| Action | Purpose |
|--------|---------|
| `ChangeWorkLogOrder` | Reassign work log |
| `CreateWorkLogReport` | Generate report |
| `DeleteWorkLogReport` | Remove report |
| `UpdateWorkLogReport` | Modify report |
| `ShareOrderWorkLogToClient` | Share with client |

### Time Tracking
| Action | Purpose |
|--------|---------|
| `StartOrderTimeTracking` | Start timer |
| `StopOrderTimeTracking` | Stop timer |

### Response Handling
| Action | Purpose |
|--------|---------|
| `AcceptResponseOnOrder` | Accept client response |
| `RejectResponseOnOrder` | Reject response |
| `DisapproveResponseOnOrder` | Disapprove response |

### Subdirectories
- `File/` - File handling actions
- `Import/` - Order import actions
- `Incoming/` - Incoming order actions
- `Outgoing/` - Outgoing order actions
- `Product/` - Product management actions
- `WorkLog/` - Work log related actions

## Target Module Structure

```
modules/orders/
├── src/
│   ├── OrdersServiceProvider.php
│   ├── Actions/
│   │   ├── Incoming/
│   │   ├── Outgoing/
│   │   ├── Product/
│   │   ├── WorkLog/
│   │   └── ...
│   ├── Contracts/
│   │   ├── External/
│   │   │   ├── UserContract.php
│   │   │   ├── CompanyContract.php
│   │   │   ├── AddressbookContract.php
│   │   │   ├── CatalogContract.php
│   │   │   └── MediaContract.php
│   │   └── Exposed/
│   │       ├── OrderLookupContract.php
│   │       └── OrderCreatorContract.php
│   ├── Models/
│   ├── Http/Controllers/
│   └── Events/
├── routes/web.php
└── tests/
```

## External Contracts Needed

| Dependency | Contract |
|------------|----------|
| User | `UserContract` |
| Company | `CompanyContract` |
| Addressbook/Client | `AddressbookContract` |
| Catalog items | `CatalogContract` |
| Media/Files | `MediaContract` |
| Invoice generation | `InvoiceContract` (from Invoicify) |
| Offer conversion | `OfferContract` (from Offer) |

## Exposed Contracts

Other modules need:

| Contract | Consumer |
|----------|----------|
| `OrderLookupContract` | Invoicify, Worklogs |
| `OrderCreatorContract` | Offer (on accept) |
| `OrderTimeTrackingContract` | Worklogs, Todos |

## Migration Priority

**HIGH** - This is the largest domain and central to the application.

## Migration Steps

1. Create `modules/orders/` structure
2. Define external contracts first
3. Create adapters in `app/Services/Orders/`
4. Migrate models
5. Migrate actions (start with leaf actions, no dependencies)
6. Migrate controllers
7. Migrate routes
8. Add tests
9. Remove old code from app/

## Integration Points

| Module | Integration |
|--------|-------------|
| Offer | Converts to order on accept |
| Invoicify | Generates invoice from order |
| Worklogs | Time logged against orders |
| Todos | Tasks linked to orders |
| Catalog | Products from catalog |
| Addressbook | Client references |
