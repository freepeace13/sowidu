---
name: invoicify-agent
description: Specialist for the Invoicify module. Handles invoice generation, PDF creation, payments, and external contracts.
tools: Read, Edit, Write, Bash, Grep, Glob
model: sonnet
---

# Invoicify Module Agent

> Inherits: [base.md](./base.md)

## Progress Tracker

| Task | Status | Notes |
|------|--------|-------|
| External contracts | 0/22 | Need UserContract, CompanyContract, OrderContract, MediaContract |
| Tests | 0% | No tests exist |
| Vite config | Inline | Alias in main vite.config.mjs |
| Frontend migration | Partial | Pages exist in both module and main app |

**Last updated**: Not yet started

---

## Domain

Invoice generation and management. Handles creating invoices from orders, manual items, PDF generation, and payment tracking.

## Scope

Only modify files within `modules/invoicify/`

## Frontend Structure

```
resources/js/
├── main.js                    # Entry point
├── bootstrap/                 # JS bootstrapping
├── Components/
│   ├── Actions/               # Action buttons
│   ├── Forms/                 # Invoice forms
│   ├── InvoicifyContainer.vue
│   ├── PdfView.vue
│   ├── PreviewActionButtons.vue
│   └── PreviewDialog.vue
├── Composables/               # Vue composables
├── Enums/                     # JS enums (mirror PHP)
├── Pages/
│   ├── Invoices/              # Invoice pages
│   └── Payments/              # Payment pages
├── Services/                  # JS services
└── types/                     # TypeScript types
```

**Vite**: Inline alias `~Invoicify` in main vite.config.mjs

**Also in main app**: `resources/js/Pages/Invoice/` (needs migration)

## Key Models

| Model | Purpose |
|-------|---------|
| `Invoice` | Main invoice entity with status, totals, client reference |
| `InvoiceItem` | Line items from orders |
| `InvoiceManualItem` | Manually added line items |

## Enums

| Enum | Values |
|------|--------|
| `InvoiceStatus` | draft, sent, paid, cancelled |
| `InvoiceKind` | Types of invoices |
| `InvoiceType` | Invoice type classification |

## Key Actions

| Action | Purpose |
|--------|---------|
| `GenerateInvoicePdfAction` | Generate PDF from invoice |
| `SendToClientAction` | Email invoice to client |
| `MarkAsPaidAction` | Mark invoice as paid |
| `AddInvoiceManualItemAction` | Add manual line item |
| `UpdateInvoiceManualItemAction` | Update manual line item |

## External Dependencies Needed

Currently has 22 `use App\` violations. Needs contracts for:

| Dependency | Contract to Create |
|------------|-------------------|
| `App\Models\User` | `UserContract` |
| `App\Models\Company` | `CompanyContract` |
| `App\Models\Order` | `OrderContract` |
| Media/PDF services | `MediaManagerContract` |

## Exposed Contracts

Other modules may need:

| Contract | Purpose |
|----------|---------|
| `GeneratesInvoiceContract` | Allow order module to trigger invoice creation |
| `InvoiceLookupContract` | Allow other modules to query invoices |

## Module-Specific Patterns

### Invoice Number Generation
- Company-specific numbering sequences
- Format: configurable per company

### PDF Generation
- Uses `PdfaInvoiceBuilder` service
- Queued for large invoices

### Calculation Service
- `InvoiceCalculationService` handles totals, taxes
- `InvoiceSummaryService` for reporting

## Priority Tasks

1. Define external contracts for all `use App\` imports
2. Create adapters in `app/Services/Invoicify/`
3. Add unit tests (currently 0)
4. This is the reference module - should be exemplary
