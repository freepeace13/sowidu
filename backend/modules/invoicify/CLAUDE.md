# Invoicify Module

> Agent: [.claude/agents/invoicify.md](/.claude/agents/invoicify.md)

This is the **reference module** for the project. Use this as a template when creating new modules.

## Progress Tracker

| Task | Status | Notes |
|------|--------|-------|
| External contracts | 0/22 | 22 `use App\` violations to fix |
| Adapters | 0/? | None created |
| Tests | 0% | No tests |
| Vite config | Inline | Alias in main vite.config.mjs |
| Frontend | Partial | Some pages in main app |

**Last updated**: 2025-01-15 - Initial tracking

## Module Overview
Handles invoice generation, PDF export, payments, and client delivery.

## Key Patterns in This Module

### Service Provider
`src/InvoicifyServiceProvider.php` handles:
- Config registration
- Contract bindings
- Policy registration
- Route loading with config-driven prefix/middleware
- View and component registration

```php
protected function registerViews()
{
    $this->loadViewsFrom(__DIR__ . '/../resources/views', 'invoicify');
    Blade::anonymousComponentPath(__DIR__ . '/../resources/views/components', 'invoicify');
}
```

### Actions Pattern
Actions are in `src/Actions/` and implement contracts from `src/Contracts/Actions/`:
- `GenerateInvoicePdfAction` - PDF generation
- `MarkAsPaidAction` - Payment status
- `SendToClientAction` - Email delivery

### Controllers Organization
Controllers are organized by feature in `src/Http/Controllers/`:
- `LineItems/` - Invoice line item management
- `Payments/` - Payment tracking
- `Deductions/` - Deduction handling
- `Documents/` - Document management
- `Taxes/` - Tax calculations

### Data Transfer Objects
DTOs in `src/Data/` for type-safe data passing:
- `InvoiceDetails`, `Item`, `Recipient`, `Sender`, etc.

### Enums
Type-safe enums in `src/Enums/`:
- `InvoiceKind`, `InvoiceStatus`, `InvoiceType`

## Resources Structure (Frontend)

```
resources/
├── css/
│   ├── invoicify.css          # Module styles
│   └── pdf.css                # PDF-specific styles
├── js/
│   ├── main.js                # Entry point
│   ├── Pages/
│   │   ├── Invoices/
│   │   │   └── Show/
│   │   │       ├── index.vue       # Main page
│   │   │       ├── Layout.vue
│   │   │       ├── Header.vue
│   │   │       ├── Invoice.vue
│   │   │       └── Partials/
│   │   │           ├── ClientInfo.vue
│   │   │           ├── InvoiceItems.vue
│   │   │           ├── InvoiceDetails.vue
│   │   │           ├── PaymentHistory.vue
│   │   │           └── ...
│   │   └── Payments/
│   ├── Components/
│   │   ├── Forms/             # Form components
│   │   │   ├── ManualItemForm.vue
│   │   │   ├── InvoiceDetailsForm.vue
│   │   │   ├── InvoiceTaxForm.vue
│   │   │   └── ...
│   │   ├── Actions/           # Action buttons
│   │   │   ├── MarkAsPaidButton.vue
│   │   │   ├── DownloadAsPdfButton.vue
│   │   │   ├── SendToClientButton.vue
│   │   │   └── ...
│   │   ├── PdfView.vue
│   │   └── PreviewDialog.vue
│   ├── Composables/
│   │   └── useEventListener.js
│   ├── Services/
│   │   └── EventBus.js
│   ├── bootstrap/
│   │   ├── index.js
│   │   ├── axios.js
│   │   ├── pinia.js
│   │   ├── vuetify.js
│   │   └── websocket.js
│   ├── types/
│   │   ├── invoice.ts
│   │   └── invoice.d.ts
│   └── Enums/
│       └── Events.js
└── views/
    ├── app.blade.php          # Inertia root view
    └── components/
        └── pdf/               # PDF Blade components
            ├── invoice.blade.php
            ├── sender.blade.php
            ├── recipient.blade.php
            ├── items-table.blade.php
            ├── total-summary.blade.php
            ├── layouts/
            │   ├── base.blade.php
            │   ├── header.blade.php
            │   └── footer.blade.php
            └── table/
                ├── index.blade.php
                ├── item.blade.php
                └── summary1.blade.php
```

## Views Usage

### Blade Views (with namespace)
```php
return view('invoicify::app');
```

### Blade Components (anonymous)
```blade
<x-invoicify::pdf.invoice :invoice="$invoice" />
<x-invoicify::pdf.table.item :item="$item" />
<x-invoicify::pdf.layouts.base>
    {{-- content --}}
</x-invoicify::pdf.layouts.base>
```

### Inertia Pages
```php
return inertia('Invoicify/Invoices/Show/index', [
    'invoice' => $invoice,
]);
```

## When Working in This Module

1. Follow the existing patterns - this module is the reference
2. Always create contracts before implementations
3. Use DTOs for data transfer between layers
4. Organize controllers by feature, not just by type
5. Write both unit and feature tests
6. **Vue Components**: Place in `resources/js/Components/`
7. **Inertia Pages**: Place in `resources/js/Pages/`
8. **Blade/PDF Components**: Place in `resources/views/components/`
