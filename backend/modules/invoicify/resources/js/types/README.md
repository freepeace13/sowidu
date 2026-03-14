# Invoice Type Definitions

This directory contains type definitions for the Invoice model and related entities used in the Invoicify module.

## Files

- `invoice.js` - JSDoc type definitions for JavaScript files
- `invoice.d.ts` - TypeScript declaration file for better IDE support
- `invoice.ts` - TypeScript interface definitions

## Usage

### In Vue Components (JavaScript)

```javascript
<script setup>
import { useForm } from '@inertiajs/vue2'

/**
 * @typedef {Object} Props
 * @property {import('./types/invoice').Invoice} invoice - The invoice object
 */
const props = defineProps({
    invoice: {
        type: Object,
        required: true,
        validator: (value) => {
            return value && 
                   typeof value.id === 'number' && 
                   typeof value.uuid === 'string' &&
                   typeof value.internal_id === 'string' &&
                   typeof value.is_paid === 'boolean' &&
                   value.type && 
                   typeof value.type.name === 'string' &&
                   typeof value.type.value === 'number'
        }
    },
})
</script>
```

### In TypeScript Files

```typescript
import type { Invoice, InvoiceType, InvoiceKind } from './types/invoice'

interface Props {
    invoice: Invoice
}

const props = defineProps<Props>()
```

### Using Constants

```javascript
import { InvoiceTypes, InvoiceKinds, InvoiceStatuses } from './types/invoice'

// Check if invoice is incoming
if (props.invoice.type.value === InvoiceTypes.INCOMING) {
    // Handle incoming invoice
}

// Check if invoice is paid
if (props.invoice.status?.is_paid) {
    // Handle paid invoice
}
```

## Invoice Interface Properties

### Required Properties
- `id: number` - Unique identifier
- `uuid: string` - UUID
- `internal_id: string` - Internal ID
- `type: InvoiceType` - Invoice type (INCOMING/OUTGOING)
- `is_paid: boolean` - Payment status

### Optional Properties
- `external_id?: string` - External ID
- `delivery_date?: string` - Delivery date
- `payment_date?: string` - Payment date
- `send_date?: string` - Send date
- `notes?: string` - Notes
- `subject?: string` - Subject
- `description?: string` - Description
- `kind?: InvoiceKind` - Invoice kind
- `status?: InvoiceStatus` - Invoice status

### Relationships
- `client?: any` - Client (Addressbook or Company)
- `order?: any` - Associated order
- `delivery_address?: any` - Delivery address

### Financial Properties
- `subtotal?: number` - Subtotal amount
- `net_amount?: number` - Net amount
- `total_vat?: number` - Total VAT
- `grand_total?: number` - Grand total

## Validation

The type definitions include validation functions that can be used in Vue prop validators to ensure the invoice object has the required structure.

## IDE Support

- **VS Code**: Install the TypeScript extension for better IntelliSense
- **WebStorm**: Native TypeScript support
- **Vim/Neovim**: Use ALE or coc.nvim with TypeScript language server

## Migration Guide

To migrate existing components:

1. Replace `type: Object` with proper validation
2. Add JSDoc comments for better IntelliSense
3. Use the provided constants instead of magic numbers
4. Consider migrating to TypeScript for better type safety 
