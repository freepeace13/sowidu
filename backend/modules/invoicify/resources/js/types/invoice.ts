export interface InvoiceType {
    name: string
    value: number
}

export interface InvoiceKind {
    value?: number
    label?: string
}

export interface InvoiceStatus {
    is_sent?: boolean
    is_paid?: boolean
    // Add other status properties as needed
}

export interface Invoice {
    id: number
    uuid: string
    internal_id: string
    external_id?: string
    delivery_date?: string
    payment_date?: string
    send_date?: string
    deliverer_id?: number
    type: InvoiceType
    notes?: string
    is_paid: boolean
    subject?: string
    description?: string
    kind?: InvoiceKind
    status?: InvoiceStatus
    // Relationships
    client?: any // Addressbook or Company
    order?: any // Order model
    delivery_address?: any // Place model
    // Additional properties that might be present
    biller_details?: any
    final_data?: any
    deduction_invoice_id?: number
    execution_period_start?: string
    execution_period_end?: string
    care_of_id?: number
    subtotal?: number
    net_amount?: number
    total_vat?: number
    grand_total?: number
    preview_layout?: any
    // Timestamps
    created_at?: string
    updated_at?: string
}
