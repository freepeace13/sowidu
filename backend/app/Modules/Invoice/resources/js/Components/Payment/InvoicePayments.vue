<script setup>
import { useDateFormat } from '@/Composables/useDayJs'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { router } from '@inertiajs/vue2'
import { useEventBus } from '@vueuse/core'
import { computed, ref } from 'vue'
import InvoicePaymentForm from './InvoicePaymentForm.vue'
import InvoicePaymentDetails from './InvoicePaymentDetails.vue'

const props = defineProps({
    invoice: {
        required: false,
        type: Object,
    },
    payments: {
        required: true,
        type: Array,
    },
    canManagePayments: {
        required: false,
        type: Boolean,
        default: false,
    },
    canViewPayments: {
        required: false,
        type: Boolean,
        default: false,
    },
    paymentsSummary: {
        required: false,
        type: Object,
        default: () => ({
            paid_formatted: 0,
            outstanding_formatted: 0,
        }),
    },
})

const { $t, $confirm, $route } = useGlobalVariables()
const invoicePaymentForm = ref(null)
const invoicePaymentDetails = ref(null)
const isLoading = computed(
    () => (props.paymentsSummary?.status_color ?? null) === null,
)

const headers = [
    {
        text: $t('invoices.payments.labels.transaction_id'),
        value: 'id',
        sortable: false,
        align: 'left',
    },
    {
        text: $t('invoices.payments.labels.amount'),
        sortable: false,
    },
    {
        text: $t('invoices.payments.labels.payment_method'),
        value: 'payment_method',
        sortable: false,
        align: 'center',
    },
    {
        text: $t('invoices.labels.payment-date'),
        value: 'payment_date',
        sortable: false,
        align: 'center',
    },
    {
        text: $t('labels.actions'),
        value: 'actions',
        sortable: false,
        align: 'right',
    },
]

const bus = useEventBus('invoice.payments.form.show')
bus.on(() => {
    invoicePaymentForm.value.show()
})

function confirmDelete(invoicePayment) {
    $confirm({
        title: $t('labels.warning'),
        question: $t('invoices.payments.messages.payment_confirm_delete'),
        type: 'warning',
        confirm: () => {
            router.delete(
                $route('invoices.payments.destroy', {
                    invoicePayment,
                    invoice: props.invoice.id,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['payments', 'invoice', 'paymentsSummary'],
                },
            )
        },
    })
}
</script>
<template>
    <div v-if="canViewPayments">
        <InvoicePaymentDetails ref="invoicePaymentDetails" />
        <InvoicePaymentForm
            ref="invoicePaymentForm"
            :invoice="invoice"
        />
        <v-flex
            xs12
            class="tw-flex tw-items-center"
        >
            <div class="tw-text-lg tw-font-semibold">
                {{ $t('invoices.labels.payments') }}:
            </div>
            <v-spacer />
            <v-btn
                v-if="canManagePayments"
                color="blue-info"
                class="white--text"
                @click="() => invoicePaymentForm.show()"
            >
                <v-icon left>payments</v-icon>
                {{ $t('invoices.buttons.add_payment') }}
            </v-btn>
        </v-flex>
        <v-flex
            xs12
            mt-2
        >
            <v-data-table
                :headers="headers"
                :items="payments"
                :total-items="payments.length"
                :hide-actions="true"
                :no-data-text="$t('invoices.payments.messages.no_payments')"
                :loading="isLoading"
                class="elevation-1 px-0 py-2 dense-header"
            >
                <template #items="{ item: payment }">
                    <tr>
                        <td>
                            <div
                                class="info--text hover:tw-underline tw-cursor-pointer tw-text-left"
                                @click="
                                    () => invoicePaymentDetails.show(payment)
                                "
                            >
                                {{ payment.id }}
                            </div>
                        </td>
                        <td class="tw-text-left">
                            {{ payment.amount_formatted }}
                        </td>
                        <td>
                            <v-chip
                                :color="payment.method_color"
                                small
                                label
                            >
                                {{ payment.method_label }}
                            </v-chip>
                        </td>
                        <td>
                            {{ useDateFormat(payment.payment_date) }}
                        </td>
                        <td class="tw-text-right">
                            <v-btn
                                flat
                                class="tw-my-0"
                                icon
                                color="error"
                                @click="confirmDelete(payment)"
                            >
                                <v-icon>delete</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                </template>

                <template #footer>
                    <tr
                        v-show="payments.length"
                        class="tw-text-sm tw-text-center"
                    >
                        <td
                            :colspan="1"
                            class="tw-text-right"
                        >
                            <strong class="tw-capitalize">
                                {{
                                    $t(
                                        'invoices.payments.labels.payment_received',
                                    )
                                }}:
                            </strong>
                        </td>
                        <td
                            class="tw-whitespace-nowrap tw-text-left tw-font-semibold"
                        >
                            {{ paymentsSummary?.paid_formatted }}
                        </td>
                    </tr>
                    <tr
                        v-show="payments.length"
                        class="tw-text-sm tw-text-center"
                        :style="{
                            backgroundColor: paymentsSummary?.status_color,
                        }"
                    >
                        <td
                            :colspan="1"
                            class="tw-text-right"
                        >
                            <strong class="tw-capitalize">
                                {{
                                    $t(
                                        'invoices.payments.labels.outstanding_balance',
                                    )
                                }}:
                            </strong>
                        </td>
                        <td
                            class="tw-whitespace-nowrap tw-text-left tw-font-semibold"
                        >
                            {{ paymentsSummary?.outstanding_formatted }}
                        </td>
                        <td :colspan="headers.length - 2" />
                    </tr>
                </template>
            </v-data-table>
        </v-flex>
    </div>
</template>
