---
name: addressbook-agent
description: Specialist for the Addressbook module (candidate). Handles contacts, organizations, persons, and addresses.
tools: Read, Edit, Write, Bash, Grep, Glob
model: sonnet
---

# Addressbook Module Agent (Candidate)

> Inherits: [base.md](./base.md)

## Progress Tracker

| Task | Status | Notes |
|------|--------|-------|
| Module created | No | Need to create modules/addressbook/ |
| Service provider | No | Need AddressbookServiceProvider |
| External contracts | 0/? | Need UserContract, CompanyContract, MediaContract, PlaceContract |
| Exposed contracts | 0/? | ContactLookupContract, OrganizationLookupContract - many modules depend on this |
| Models migrated | 0/5 | Contact, Organization, Person, Address, etc. |
| Actions migrated | 0/14 | |
| Controllers migrated | 0/15 | 8 Inertia + 7 Json |
| Repositories migrated | 0/4 | Already have repository pattern |
| Routes migrated | No | routes/addressbook.php |
| Tests | 0 | No tests |
| Vite config | No | Need addressbook.vite.mjs |
| Frontend migrated | 0/? | Organization, Person, Partials, Trash |

**Last updated**: Not yet started

---

## Status: NOT YET MODULARIZED

This is a candidate for modularization. Code currently lives in main app.

## Domain

Contact and client management. Handles organizations, persons, addresses, and contact information.

## Frontend (to migrate)

Currently in `resources/js/Pages/Addressbook/`:

```
Addressbook/
├── AddressbookLayout.vue      # Main layout
├── Organization/              # Organization pages
├── Partials/                  # Shared partials
├── Person/                    # Person/contact pages
└── Trash/                     # Deleted items view
```

## Current Location

| Component | Location | Count |
|-----------|----------|-------|
| Inertia Controllers | `app/Http/Controllers/Inertia/Addressbook/` | 8 |
| Json Controllers | `app/Http/Controllers/Json/Addressbook/` | 7 |
| Actions | `app/Actions/Addressbook/` | 14 |
| Models | `app/Models/` | ~5 |
| Events | `app/Events/Addressbook/` | 3 |
| Repositories | `app/Repositories/` | 4 |
| Routes | `routes/addressbook.php` | 1 |

## Key Actions (to migrate)

| Action | Purpose |
|--------|---------|
| `AddressbookAction` | Base addressbook action |
| `CreateCareOfActionAddressbook` | Create care-of address |
| `RestoreAddressbook` | Restore deleted entry |

### Subdirectories
- `Organization/` - Company/org management
- `Person/` - Individual contact management

## Target Module Structure

```
modules/addressbook/
├── src/
│   ├── AddressbookServiceProvider.php
│   ├── Actions/
│   │   ├── Organization/
│   │   └── Person/
│   ├── Contracts/
│   │   ├── External/
│   │   │   ├── UserContract.php
│   │   │   ├── CompanyContract.php
│   │   │   ├── MediaContract.php
│   │   │   └── PlaceContract.php
│   │   └── Exposed/
│   │       ├── ContactLookupContract.php
│   │       ├── OrganizationLookupContract.php
│   │       └── AddressResolverContract.php
│   ├── Models/
│   │   ├── Contact.php
│   │   ├── Organization.php
│   │   ├── Person.php
│   │   └── Address.php
│   ├── Repositories/
│   └── Http/Controllers/
├── routes/web.php
└── tests/
```

## External Contracts Needed

| Dependency | Contract |
|------------|----------|
| User | `UserContract` |
| Company | `CompanyContract` |
| Media/Avatar | `MediaContract` |
| Place/Location service | `PlaceContract` |

## Exposed Contracts

Many modules need addressbook data:

| Contract | Consumers |
|----------|-----------|
| `ContactLookupContract` | Orders, Offers, Invoicify |
| `OrganizationLookupContract` | Orders, Offers |
| `AddressResolverContract` | Orders, DeliveryTicket |
| `ClientSearchContract` | All client-facing modules |

## Migration Priority

**HIGH** - Self-contained and many modules depend on it.

## Migration Steps

1. Create `modules/addressbook/` structure
2. Define exposed contracts first (other modules depend on this)
3. Define external contracts
4. Migrate repositories (already exist)
5. Migrate models
6. Migrate actions
7. Migrate controllers
8. Update consuming modules to use contracts
9. Add tests

## Integration Points

| Module | Integration |
|--------|-------------|
| Orders | Client/recipient lookup |
| Offers | Client lookup |
| Invoicify | Client on invoice |
| DeliveryTicket | Delivery addresses |
| Media | Contact avatars |

## Notes

- This module is a dependency for many others
- Should be migrated early in the modularization process
- Exposed contracts are more important than external contracts here
