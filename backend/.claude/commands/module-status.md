# Module Status

Check and display the current modularization status of the project.

## Instructions

When this command is invoked:

1. **Scan the modules folder** to list all existing modules
2. **Check each module's completeness**:
   - Has service provider?
   - Has routes?
   - Has external contracts defined?
   - Has tests?
3. **Identify candidates** for modularization from main app
4. **Display a status report**

## Status Check Criteria

### Module Completeness Levels

**Complete** (Ready for production):
- [x] Service provider registered
- [x] Routes self-contained in module
- [x] External contracts defined for all dependencies
- [x] Adapters implemented in main app
- [x] Tests exist and pass
- [x] Documentation complete

**Partial** (In progress):
- [x] Basic structure exists
- [x] Some code migrated
- [ ] External contracts incomplete
- [ ] Routes still in main app
- [ ] Tests incomplete

**Minimal** (Just started):
- [x] Folder exists
- [ ] Most code still in main app
- [ ] No external contracts
- [ ] No tests

**Planned** (Not started):
- [ ] Code exists in main app
- [ ] No module folder yet

## Expected Output Format

```
## Module Status Report

### Existing Modules

| Module | Status | Provider | Routes | Contracts | Tests | Vite |
|--------|--------|----------|--------|-----------|-------|------|
| invoicify | Complete | Yes | Yes | Yes | Yes | Yes |
| offer | Complete | Yes | Yes | Partial | Yes | Yes |
| catalog | Partial | Yes | Yes | Yes | No | Inline |
| chatly | Partial | Yes | No | Yes | Partial | Inline |
| todos | Partial | Yes | No | No | Partial | Yes |
| worklogs | Partial | Yes | No | No | No | Yes |
| company | Minimal | No | No | No | No | No |
| deliveryticket | Minimal | No | No | No | No | No |
```

## Detailed Checklists Per Module

### INVOICIFY (Reference - Complete)
- [x] Service provider registered
- [x] Routes self-contained in module
- [x] External contracts defined
- [x] Adapters in main app
- [x] Vite config (inline in vite.config.mjs)
- [x] Views/Blade components
- [x] Vue/Inertia pages
- [x] Unit tests
- [x] Feature tests
- [x] DTOs defined
- [x] Enums defined
- [x] Policies

### OFFER (Complete)
- [x] Service provider registered
- [x] OfferEventServiceProvider for events
- [x] Routes self-contained
- [x] Vite config (`offer.vite.mjs`)
- [x] Mail templates
- [x] Notifications
- [x] Tests
- [ ] External contracts (uses some main app models directly)

### CATALOG (Partial)
- [x] Service provider registered
- [x] Routes in module
- [x] External contracts defined (Permission, CompanyInfo, MediaManager)
- [x] Vite alias (inline `~Catalog`)
- [ ] Vite config file (`catalog.vite.mjs`)
- [ ] Complete test coverage
- [ ] Vue pages (may still be in main app)
- [ ] Adapters fully implemented in main app

### CHATLY (Partial)
- [x] Service provider registered
- [x] External contracts comprehensive (6 contracts)
- [x] Vite alias (inline `~Chatly`)
- [x] Repository pattern implemented
- [x] API resources
- [ ] Routes NOT active (commented in web.php)
- [ ] Vite config file (`chatly.vite.mjs`)
- [ ] Routes need migration from `routes/chat.php`
- [ ] Complete test coverage
- [ ] Adapters for all 6 contracts in main app

### TODOS (Partial)
- [x] Multiple service providers (Todo, Auth, Route)
- [x] Vite config (`todos.vite.mjs`)
- [x] Rich event system (20+ events)
- [x] Nested action structure
- [ ] Routes NOT active (commented in web.php)
- [ ] Routes need migration from `routes/todo.php`
- [ ] External contracts NOT defined
- [ ] Uses main app models directly (User, Company)
- [ ] Complete test coverage

### WORKLOGS (Partial)
- [x] Multiple service providers (WorkLog, Binding, Policy, Route)
- [x] Action contracts defined
- [x] Vite config (`worklogs.vite.mjs`)
- [ ] Routes NOT active (commented in web.php)
- [ ] Routes need migration from `routes/work_log.php`
- [ ] External contracts NOT defined
- [ ] Uses main app models directly
- [ ] Test coverage

### COMPANY (Minimal)
- [x] Models folder exists
- [ ] No service provider
- [ ] No routes
- [ ] No external contracts
- [ ] No actions
- [ ] No controllers
- [ ] No tests
- [ ] No Vite config

### DELIVERYTICKET (Minimal)
- [x] Folder structure exists
- [x] Config folder
- [x] Routes folder (empty)
- [ ] No service provider
- [ ] No src/ folder
- [ ] No models
- [ ] No actions
- [ ] No tests
- [ ] No Vite config

## Candidates for Modularization (Main App)

### ORDERS (High Priority)
**Current Location**: `app/Http/Controllers/Inertia/Order/`, `app/Actions/Order/`
**Complexity**: High (largest domain)

| Component | Count | Location |
|-----------|-------|----------|
| Inertia Controllers | 14 | `app/Http/Controllers/Inertia/Order/` |
| Json Controllers | 3 | `app/Http/Controllers/Json/Order/` |
| Actions | 32 | `app/Actions/Order/` |
| Models | 3 | `app/Models/Order*` |
| Events | 11 | `app/Events/Order/` |
| Listeners | 6 | `app/Listeners/Order/` |
| Routes | 1 file | `routes/order.php` |

**Dependencies to Extract**:
- [ ] User model → UserContract
- [ ] Company model → CompanyContract
- [ ] Addressbook → AddressbookContract
- [ ] Invoice integration → InvoiceContract
- [ ] Offer integration → OfferContract
- [ ] Media → MediaContract

### ADDRESSBOOK (High Priority)
**Current Location**: `app/Http/Controllers/Inertia/Addressbook/`, `app/Actions/Addressbook/`

| Component | Count | Location |
|-----------|-------|----------|
| Inertia Controllers | 8 | `app/Http/Controllers/Inertia/Addressbook/` |
| Json Controllers | 7 | `app/Http/Controllers/Json/Addressbook/` |
| Actions | 14 | `app/Actions/Addressbook/` |
| Models | ~5 | `app/Models/Addressbook*`, Contact, etc. |
| Events | 3 | `app/Events/Addressbook/` |
| Repositories | 4 | `app/Repositories/` |
| Routes | 1 file | `routes/addressbook.php` |

**Dependencies to Extract**:
- [ ] User model → UserContract
- [ ] Company model → CompanyContract
- [ ] Media/Avatar → MediaContract
- [ ] Place service → PlaceContract

### MEDIA (Medium Priority)
**Current Location**: `app/Http/Controllers/Inertia/Media/`, `app/Actions/Media/`

| Component | Count | Location |
|-----------|-------|----------|
| Inertia Controllers | 8 | `app/Http/Controllers/Inertia/Media/` |
| Json Controllers | 2 | `app/Http/Controllers/Json/Media/` |
| Actions | 15 | `app/Actions/Media/` |
| Models | 3 | `app/Models/Media*`, Attachment |
| Events | 4 | `app/Events/Media/` |
| Jobs | 3 | `app/Jobs/Media/` |
| Routes | 2 files | `routes/media.php`, `routes/media_library.php` |

### ORGANIZATION (Medium Priority)
**Current Location**: `app/Http/Controllers/Inertia/Organization/`, `app/Actions/Organization/`

| Component | Count | Location |
|-----------|-------|----------|
| Inertia Controllers | 12 | `app/Http/Controllers/Inertia/Organization/` |
| Actions | 16 | `app/Actions/Organization/` |
| Models | 6 | Company, Employee, Role, Permission, etc. |
| Events | 4 | `app/Events/Organization/` |
| Routes | 1 file | `routes/account.php` (partial) |

### DELIVERY TICKETS (Medium Priority)
**Current Location**: `app/Http/Controllers/Inertia/DeliveryTicket/`

| Component | Count | Location |
|-----------|-------|----------|
| Inertia Controllers | 3 | `app/Http/Controllers/Inertia/DeliveryTicket/` |
| Json Controllers | 3 | `app/Http/Controllers/Json/DeliveryTicket/` |
| Actions | 10 | `app/Actions/DeliveryTicket/` |
| Models | 3 | DeliveryTicket, Material, Document |
| Events | 2 | `app/Events/DeliveryTicket/` |
| Routes | 1 file | `routes/delivery_ticket.php` |

**Note**: Module folder exists at `modules/DeliveryTicket/` but is empty.

## Next Steps Priority

1. **Activate existing modules**:
   - [ ] Uncomment chatly routes in web.php
   - [ ] Uncomment todos routes in web.php
   - [ ] Uncomment worklogs routes in web.php
   - [ ] Move route files into modules

2. **Complete partial modules**:
   - [ ] Add external contracts to todos
   - [ ] Add external contracts to worklogs
   - [ ] Add test coverage to catalog
   - [ ] Create Vite configs for chatly, catalog

3. **Start new modules**:
   - [ ] Orders (highest impact)
   - [ ] Addressbook (self-contained)
   - [ ] Complete DeliveryTicket module

## How to Check

### Check if module has service provider:
```bash
ls modules/{name}/src/*ServiceProvider.php
# or
ls modules/{name}/src/Providers/*ServiceProvider.php
```

### Check if routes are self-contained:
```bash
# Module routes exist
ls modules/{name}/routes/web.php

# Main app routes don't reference module controllers
grep -l "Modules\\{Name}" routes/*.php
```

### Check for external contracts:
```bash
ls modules/{name}/src/Contracts/External/
```

### Check for tests:
```bash
ls modules/{name}/tests/
```

## Run Analysis

When invoked, Claude should:
1. Run the checks above
2. Generate the status table
3. Provide actionable recommendations
