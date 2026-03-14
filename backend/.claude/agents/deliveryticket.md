---
name: deliveryticket-agent
description: Specialist for the DeliveryTicket module (candidate). Handles delivery tickets, materials, and documents.
tools: Read, Edit, Write, Bash, Grep, Glob
model: sonnet
---

# DeliveryTicket Module Agent (Candidate)

> Inherits: [base.md](./base.md)

## Progress Tracker

| Task | Status | Notes |
|------|--------|-------|
| Module created | Partial | Empty folder exists at modules/DeliveryTicket/ (wrong case) |
| Service provider | No | Need DeliveryTicketServiceProvider |
| External contracts | 0/? | Need UserContract, CompanyContract, OrderContract, AddressbookContract, MediaContract |
| Exposed contracts | 0/? | DeliveryTicketLookupContract |
| Models migrated | 0/3 | DeliveryTicket, Material, Document |
| Actions migrated | 0/10 | |
| Controllers migrated | 0/6 | 3 Inertia + 3 Json |
| Routes migrated | No | routes/delivery_ticket.php |
| Tests | 0 | No tests |
| Vite config | No | Need deliveryticket.vite.mjs |
| Frontend migrated | 0/? | Two locations - standalone + under Order |

**Last updated**: Not yet started

---

## Status: PARTIALLY STARTED

Module folder exists at `modules/DeliveryTicket/` but is empty. Code lives in main app.

## Domain

Delivery ticket management. Handles delivery documentation, materials tracking, and document attachments for deliveries.

## Frontend (to migrate)

Currently in two locations:

**Main location** (`resources/js/Pages/DeliveryTicket/`):
```
DeliveryTicket/
├── Components/                # DT-specific components
├── Index.vue                  # List view
└── Show.vue                   # Detail view
```

**Nested under Order** (`resources/js/Pages/Order/DeliveryTicket/`):
- Delivery ticket views accessed from order context
- May need to remain accessible from orders module

## Current Location

| Component | Location | Count |
|-----------|----------|-------|
| Inertia Controllers | `app/Http/Controllers/Inertia/DeliveryTicket/` | 3 |
| Json Controllers | `app/Http/Controllers/Json/DeliveryTicket/` | 3 |
| Actions | `app/Actions/DeliveryTicket/` | 10 |
| Models | `app/Models/` | 3 |
| Events | `app/Events/DeliveryTicket/` | 2 |
| Routes | `routes/delivery_ticket.php` | 1 |
| Empty Module | `modules/DeliveryTicket/` | - |

## Key Actions (to migrate)

| Action | Purpose |
|--------|---------|
| `CreateDeliveryTicket` | Create new ticket |
| `UpdateDeliveryTicket` | Modify ticket |
| `DeleteDeliveryTicket` | Remove ticket |
| `AddMaterialToDeliveryTicket` | Add material line |
| `UpdateDeliveryTicketMaterial` | Update material |
| `RemoveMaterialOnDeliveryTicket` | Remove material |
| `AddDocumentToDeliveryTicket` | Attach document |
| `RemoveDocumentToDeliveryTicket` | Remove document |

### Subdirectories
- `Import/` - Import from external sources

## Key Models (to migrate)

| Model | Purpose |
|-------|---------|
| `DeliveryTicket` | Main ticket entity |
| `DeliveryTicketMaterial` | Material line items |
| `DeliveryTicketDocument` | Attached documents |

## Target Module Structure

```
modules/deliveryticket/  # lowercase to match convention
├── src/
│   ├── DeliveryTicketServiceProvider.php
│   ├── Actions/
│   │   ├── Import/
│   │   ├── Material/
│   │   └── Document/
│   ├── Contracts/
│   │   ├── External/
│   │   │   ├── UserContract.php
│   │   │   ├── CompanyContract.php
│   │   │   ├── OrderContract.php
│   │   │   ├── AddressbookContract.php
│   │   │   └── MediaContract.php
│   │   └── Exposed/
│   │       └── DeliveryTicketLookupContract.php
│   ├── Models/
│   │   ├── DeliveryTicket.php
│   │   ├── Material.php
│   │   └── Document.php
│   └── Http/Controllers/
├── routes/web.php
└── tests/
```

## External Contracts Needed

| Dependency | Contract |
|------------|----------|
| User | `UserContract` |
| Company | `CompanyContract` |
| Order | `OrderContract` |
| Addressbook | `AddressbookContract` |
| Media/Documents | `MediaContract` |

## Exposed Contracts

| Contract | Consumers |
|----------|-----------|
| `DeliveryTicketLookupContract` | Orders |

## Migration Priority

**MEDIUM** - Relatively self-contained, good candidate for practice.

## Migration Steps

1. Rename `modules/DeliveryTicket/` to `modules/deliveryticket/` (lowercase convention)
2. Create proper structure in module
3. Define external contracts
4. Migrate models
5. Migrate actions
6. Migrate controllers
7. Move routes to module
8. Add tests

## Integration Points

| Module | Integration |
|--------|-------------|
| Orders | Delivery tickets for orders |
| Addressbook | Delivery addresses |
| Media | Document attachments |

## Notes

- Good candidate for modularization practice
- Relatively small scope
- Empty module folder already exists (wrong case)
- Rename to lowercase `deliveryticket` to match convention
