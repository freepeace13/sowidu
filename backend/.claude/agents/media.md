---
name: media-agent
description: Specialist for the Media module (candidate). Handles file uploads, image processing, sharing, and media library.
tools: Read, Edit, Write, Bash, Grep, Glob
model: sonnet
---

# Media Module Agent (Candidate)

> Inherits: [base.md](./base.md)

## Progress Tracker

| Task | Status | Notes |
|------|--------|-------|
| Module created | No | Need to create modules/media/ |
| Service provider | No | Need MediaServiceProvider |
| External contracts | 0/? | Need UserContract, CompanyContract, StorageContract |
| Exposed contracts | 0/? | MediaManagerContract - critical, many modules need this |
| Models migrated | 0/3 | Media, MediaCategory, Attachment |
| Actions migrated | 0/15 | |
| Controllers migrated | 0/10 | 8 Inertia + 2 Json |
| Jobs migrated | 0/3 | Background processing |
| Routes migrated | No | routes/media.php, routes/media_library.php |
| Tests | 0 | No tests |
| Vite config | No | Need media.vite.mjs |
| Frontend migrated | 0/? | Index, Starred, Trash, Partials |

**Last updated**: Not yet started

---

## Status: NOT YET MODULARIZED

This is a candidate for modularization. Code currently lives in main app.

## Domain

File and media management. Handles uploads, image processing, sharing, tagging, and media library.

## Frontend (to migrate)

Currently in `resources/js/Pages/Media/`:

```
Media/
├── Index.vue                  # Main media library (15KB - large file)
├── Partials/                  # Shared partials
├── Starred.vue                # Starred items
└── Trash.vue                  # Deleted items
```

## Current Location

| Component | Location | Count |
|-----------|----------|-------|
| Inertia Controllers | `app/Http/Controllers/Inertia/Media/` | 8 |
| Json Controllers | `app/Http/Controllers/Json/Media/` | 2 |
| Actions | `app/Actions/Media/` | 15 |
| Models | `app/Models/` | 3 |
| Events | `app/Events/Media/` | 4 |
| Jobs | `app/Jobs/Media/` | 3 |
| Routes | `routes/media.php`, `routes/media_library.php` | 2 |

## Key Actions (to migrate)

| Action | Purpose |
|--------|---------|
| `DeleteMedia` | Remove media file |
| `ConvertHeicToJpg` | Image format conversion |
| `NormalizeImages` | Image processing |
| `CreateMediaAddressTag` | Tag with address |
| `TagMediaWithCategory` | Categorize media |
| `RemoveTagMediaCategory` | Remove category |

### Subdirectories
- `AutoShare/` - Automatic sharing rules
- `Share/` - Manual sharing actions

## Target Module Structure

```
modules/media/
├── src/
│   ├── MediaServiceProvider.php
│   ├── Actions/
│   │   ├── AutoShare/
│   │   ├── Share/
│   │   └── Processing/
│   ├── Contracts/
│   │   ├── External/
│   │   │   ├── UserContract.php
│   │   │   ├── CompanyContract.php
│   │   │   └── StorageContract.php
│   │   └── Exposed/
│   │       ├── MediaManagerContract.php
│   │       ├── ImageProcessorContract.php
│   │       └── MediaUploaderContract.php
│   ├── Models/
│   │   ├── Media.php
│   │   ├── MediaCategory.php
│   │   └── Attachment.php
│   ├── Jobs/
│   └── Http/Controllers/
├── routes/web.php
└── tests/
```

## External Contracts Needed

| Dependency | Contract |
|------------|----------|
| User | `UserContract` |
| Company | `CompanyContract` |
| Storage service | `StorageContract` |

## Exposed Contracts

Critical - many modules need media:

| Contract | Consumers |
|----------|-----------|
| `MediaManagerContract` | All modules with file uploads |
| `MediaUploaderContract` | Orders, Chatly, Todos |
| `ImageProcessorContract` | Any image handling |
| `AttachmentContract` | Orders, DeliveryTicket |

## Migration Priority

**MEDIUM** - Many modules depend on it, but can use adapters initially.

## Migration Steps

1. Create `modules/media/` structure
2. Define exposed contracts first (critical dependency)
3. Migrate jobs (background processing)
4. Migrate models
5. Migrate actions
6. Update all consuming modules to use `MediaManagerContract`
7. Add tests

## Integration Points

| Module | Integration |
|--------|-------------|
| Chatly | Message attachments |
| Todos | Task attachments |
| Orders | Order documents |
| Addressbook | Contact avatars |
| Catalog | Product images |
| DeliveryTicket | Ticket documents |
| Invoicify | Invoice PDFs |
| Offer | Offer PDFs |

## Notes

- This is a shared service module
- Exposed contracts are the primary focus
- Already have `MediaManagerContract` in chatly/catalog - can standardize
- Image processing should be queued
