<script setup>
import { router, usePage } from '@inertiajs/vue2'
import { computed } from 'vue'
import Events from '~Invoicify/Enums/Events'
import { EventBus } from '~Invoicify/Services/EventBus'
import { authCan, canAll } from '~Shared/Composables/useAuth'
import useGlobalVariables from '~Shared/Composables/useGlobalVariables'
import { getPageProps } from '~Shared/Composables/useUtils'

const emit = defineEmits(['click:add-item'])

const { items, invoice } = usePage()
const { $t, $confirm, $route } = useGlobalVariables()

const invoiceIsEditable = computed(() =>
    getPageProps('permissions.invoice_is_editable'),
)

const showAddItemForm = () => emit('click:add-item', invoice)
const showTaxForm = () => {
    EventBus.$emit(Events.TOGGLE_TAX_FORM, {
        invoice: invoice.id,
    })
}

function confirmRemovingTax(tax) {
    $confirm({
        title: $t('invoices.labels.remove-tax'),
        question: $t('invoices.message.tax.confirm_removing'),
        type: 'warning',
        confirm: () => {
            router.delete(
                $route('invoices.taxes.destroy', {
                    invoice: invoice.value,
                    tax,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['invoice', 'invoiceSummary'],
                },
            )
        },
    })
}
</script>

<template>
    <v-flex xs12>
        <InvoiceItems
            v-if="invoice?.id"
            card-text-class="px-0"
            :items="items"
            :invoice="invoice"
            :show-update-quantity="invoiceIsEditable"
            :tax-editable="authCan('can_manage_taxes') && invoiceIsEditable"
            :show-actions="
                invoiceIsEditable &&
                canAll(['can_update_items', 'invoice_is_editable'])
            "
            @click:add-item="showAddItemForm"
            @click:remove-tax="confirmRemovingTax"
            @click:add-tax="showTaxForm"
        />
    </v-flex>
</template>
