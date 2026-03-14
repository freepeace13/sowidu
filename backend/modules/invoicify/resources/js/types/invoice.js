/**
 * @typedef {Object} InvoiceType
 * @property {string} name - The display name of the invoice type
 * @property {number} value - The numeric value of the invoice type
 */

/**
 * @typedef {Object} InvoiceKind
 * @property {number} [value] - The numeric value of the invoice kind
 * @property {string} [label] - The display label of the invoice kind
 */

/**
 * @typedef {Object} InvoiceStatus
 * @property {boolean} [is_draft] - Whether the invoice is a draft
 * @property {boolean} [is_paid] - Whether the invoice has been paid
 */

/**
 * @typedef {Object} Invoice
 * @property {number} id - The unique identifier of the invoice
 * @property {string} uuid - The UUID of the invoice
 * @property {string} internal_id - The internal ID of the invoice
 * @property {string} [external_id] - The external ID of the invoice
 * @property {string} [delivery_date] - The delivery date of the invoice
 * @property {string} [payment_date] - The payment date of the invoice
 * @property {string} [send_date] - The send date of the invoice
 * @property {number} [deliverer_id] - The ID of the deliverer
 * @property {InvoiceType} type - The type of the invoice
 * @property {string} [notes] - Notes for the invoice
 * @property {boolean} is_paid - Whether the invoice is paid
 * @property {string} [subject] - The subject of the invoice
 * @property {string} [description] - The description of the invoice
 * @property {InvoiceKind} [kind] - The kind of the invoice
 * @property {InvoiceStatus} [status] - The status of the invoice
 * @property {Object} [client] - The client (Addressbook or Company)
 * @property {Object} [order] - The associated order
 * @property {Object} [delivery_address] - The delivery address (Place)
 * @property {Object} [biller_details] - The biller details
 * @property {Object} [final_data] - The final data
 * @property {number} [deduction_invoice_id] - The ID of the deduction invoice
 * @property {string} [execution_period_start] - The start of the execution period
 * @property {string} [execution_period_end] - The end of the execution period
 * @property {number} [care_of_id] - The care of ID
 * @property {number} [subtotal] - The subtotal amount
 * @property {number} [net_amount] - The net amount
 * @property {number} [total_vat] - The total VAT amount
 * @property {number} [grand_total] - The grand total amount
 * @property {Object} [preview_layout] - The preview layout
 * @property {string} [created_at] - The creation timestamp
 * @property {string} [updated_at] - The last update timestamp
 */

// Export for use in other files
export const InvoiceTypes = {
    INCOMING: 1,
    OUTGOING: 2,
}

export const InvoiceKinds = {
    PARTIAL_1: 1,
    PARTIAL_2: 2,
}

export const InvoiceStatuses = {
    DRAFT: 0,
    SENT: 1,
    PAID: 2,
    PARTIALLY_PAID: 3,
    OVERPAID: 4,
}
