# Modularization Status Context

Load this context with `/module-status` to see current state.

## Existing Modules

| Module | Status | Provider | Routes | Contracts | Tests | Vite |
|--------|--------|----------|--------|-----------|-------|------|
| invoicify | Complete | Yes | Yes | Yes | Yes (18) | Inline |
| offer | Complete | Yes (2) | Yes | Partial | Yes (4) | Config |
| catalog | Partial | Yes | Yes | Yes (3) | Yes (8) | Inline |
| chatly | Partial | Yes | Yes | Yes (6) | Yes (6) | Inline |
| todos | Partial | Yes (3) | Yes | No | No | Config |
| worklogs | Partial | Yes (4) | Yes | No | Yes (2) | Config |
| shared | Partial | Yes | No | No | No | No |
| Company | Minimal | No | No | No | No | No |
| DeliveryTicket | Minimal | No | No | No | No | No |

## Frontend Resources

| Module | Blade Views | Vue Components | JS Files | CSS Files |
|--------|-------------|----------------|----------|-----------|
| invoicify | 17 | 37 | 10 | 2 |
| offer | 9 | 17 | 2 | 3 |
| todos | 1 | 64 | 7 | 2 |
| chatly | 1 | 29 | 3 | 1 |
| worklogs | 1 | 8 | 3 | 2 |
| catalog | 0 | 5 | 0 | 1 |
| shared | 1 | 5 | 6 | 0 |
| Company | 0 | 0 | 0 | 0 |
| DeliveryTicket | 0 | 0 | 0 | 0 |

**Frontend Notes:**
- **invoicify** - Most complete: PDF templates, bootstrap (axios, pinia, vuetify, websocket), composables
- **offer** - Complete: Mail/PDF blade templates, 11 form components
- **todos** - Rich Vue (64 components) but only 1 blade view, missing external contracts
- **chatly** - Rich Vue (29 components) with attachment viewers, message partials
- **worklogs** - Complete frontend with forms, toolbar, reports
- **catalog** - Minimal: No blade views, no JS entry point, needs work
- **shared** - Utility module: Layouts, common composables (useAuth, useDayJs, useUtils)

## Adapters Status (app/Services/)

| Module | Adapters | Location |
|--------|----------|----------|
| chatly | 6 | `app/Services/Chat/` |
| catalog | 3 | `app/Services/Catalog/` |
| todos | 0 | Needs implementation |
| worklogs | 0 | Needs implementation |

## Immediate Tasks

1. [ ] Add external contracts to todos (User, Company dependencies)
2. [ ] Add external contracts to worklogs (User, Company dependencies)
3. [ ] Create `chatly.vite.mjs` config file
4. [ ] Create `catalog.vite.mjs` config file
5. [ ] Add test coverage to todos and worklogs
6. [ ] Add blade views to catalog
7. [ ] Create Documentation module with multi-language support (German priority)
8. [ ] Integrate Documentation module with existing translation package

## Candidates for New Modules

| Feature | Controllers | Actions | Priority |
|---------|-------------|---------|----------|
| Orders | 18 | 18 | High |
| **Documentation** | - | - | **High** |
| Organization | 14 | 7 | Medium |
| Media | 11 | 8 | Medium |
| Addressbook | 9 | 5 | Medium |
| DeliveryTicket | 3 | 9 | Medium |

## Documentation Module (Planned)

A new module for user documentation and tutorials with multi-language support.

**Features**:
- User guide generation and management
- Step-by-step tutorials with screenshots
- Multi-language support (German priority)
- API documentation generation
- In-app help system
- Search functionality

**Structure**:
```
modules/documentation/
├── src/
│   ├── Actions/
│   │   ├── GenerateUserGuideAction.php
│   │   ├── GenerateTutorialAction.php
│   │   └── TranslateDocumentAction.php
│   ├── Contracts/
│   │   └── External/
│   │       └── TranslationServiceContract.php
│   ├── Models/
│   │   ├── Document.php
│   │   ├── Tutorial.php
│   │   └── Translation.php
│   ├── Services/
│   │   ├── MarkdownRenderer.php
│   │   └── DocumentationSearch.php
│   └── Http/
│       └── Controllers/
├── resources/
│   ├── js/
│   │   ├── Components/
│   │   └── Pages/
│   └── views/
│       └── pdf/
├── docs/
│   ├── en/
│   ├── de/
│   └── nl/
└── tests/
```

**Integration with Translation Package**:
Uses existing `packages/translation/` for language management.
