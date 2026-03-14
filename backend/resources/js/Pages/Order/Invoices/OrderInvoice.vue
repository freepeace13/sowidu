<script>
import useGlobalVariables from '@/Composables/useGlobalVariables'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import InvoiceDocuments from '@/Pages/Invoice/Components/InvoiceDocuments.vue'
import InvoicePayments from '@/Pages/Invoice/Components/InvoicePayments.vue'
import InvoiceTaxForm from '@/Pages/Invoice/Components/InvoiceTaxForm.vue'
import { router } from '@inertiajs/vue2'
import { get } from '@vueuse/core'
import PreviewButton from '~Invoicify/Components/Actions/PreviewButton.vue'
import InvoicifyContainer from '~Invoicify/Components/InvoicifyContainer.vue'
import OrderLayout from '../OrderLayout.vue'

export default {
    layout: [AuthLayout, OrderLayout],
}
</script>
<script setup>
import { authCan } from '@/Composables/useAuth'
import { useDateFormat } from '@/Composables/useDayJs'
import InvoiceItems from '@/Pages/Invoice/Components/InvoiceItems.vue'
import { Link } from '@inertiajs/vue2'
import { computed, provide, ref, toRef } from 'vue'
import InvoiceItemForm from '../../Invoice/Components/InvoiceItemForm.vue'

const props = defineProps({
    invoice: {
        required: true,
        type: Object,
    },
    items: {
        required: true,
        type: Array,
    },
    documents: {
        required: true,
        type: Array,
    },
    order: {
        type: Object,
    },
    permissions: {
        required: true,
        type: Object,
    },
    warnEmployeeRate: {
        required: true,
        type: Boolean,
    },
    payments: {
        required: false,
        type: Array,
        default: () => [],
    },
    paymentsSummary: {
        required: false,
        type: Object,
        default: () => ({
            paid: 0,
            outstanding: 0,
        }),
    },
})

const { $confirm, $t, $route } = useGlobalVariables()
const invoiceTaxFormRef = ref(null)

const manualItemFormRef = ref(null)
const invoice = toRef(props, 'invoice')
const invoiceItemFormRef = ref(null)
const client = computed(() => invoice.value?.client)
const invoiceIsNotPaid = computed(() => !invoice.value.is_paid)
const invoiceIsSent = computed(() => invoice.value.status.is_sent)
const itemsCanBeUpdated = computed(
    () =>
        invoiceIsNotPaid.value &&
        !invoiceIsSent.value &&
        (get(props, 'permissions')?.can_update_items ?? false),
)

const invoiceCanBeUpdated = computed(() => itemsCanBeUpdated.value)

const authCanUpdateDocuments = computed(
    () =>
        (invoiceIsNotPaid.value || invoiceIsSent) &&
        (get(props, 'permissions')?.can_update_documents ?? false),
)

function confirmSendToClient() {
    $confirm({
        title: $t('labels.warning'),
        question: $t('invoices.message.confirm_send_to_client'),
        type: 'info',
        confirm: () => {
            router.post(
                window.route('invoices.send_to_client', {
                    invoice: invoice.value,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['invoice'],
                },
            )
        },
    })
}

const addTax = () => invoiceTaxFormRef.value.show(props.invoice)

function confirmMarkingAsPaid() {
    $confirm({
        title: $t('labels.warning'),
        question: $t('invoices.message.confirm_marking_as_paid'),
        type: 'warning',
        confirm: () => {
            router.post(
                window.route('invoices.mark_as_paid', {
                    invoice: invoice.value,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: [
                        'errors',
                        'flash',
                        'invoice',
                        'payments',
                        'paymentsSummary',
                    ],
                    onSuccess: () => {
                        this.fetch()
                    },
                },
            )
        },
    })
}

const careOf = computed(() =>
    invoice.value.care_of_address && invoice.value.care_of_name
        ? [
              invoice.value.care_of_name,
              invoice.value.care_of_legalform,
              invoice.value.care_of_address,
          ]
              .filter(Boolean)
              .join(' ')
        : null,
)

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

provide('addTax', addTax)
provide('confirmRemovingTax', confirmRemovingTax)
</script>
<template>
    <InvoicifyContainer>
        <div class="fill-height tw-w-full">
            <InvoiceTaxForm ref="invoiceTaxFormRef" />
            <ManualItemForm
                ref="manualItemFormRef"
                :invoice="invoice"
                @refresh="
                    router.reload({
                        only: ['invoice', 'items', 'invoiceSummary'],
                    })
                "
            />
            <portal
                to="toolbar"
                tag="span"
            >
                <v-toolbar
                    id="dropdown-example"
                    absolute
                    top
                    flat
                    color="white"
                >
                    <v-btn
                        v-tooltip.top="`${$t('buttons.go-back')}`"
                        icon
                        class="hidden-xs-only"
                        @click="
                            $inertia.get(
                                $route('orders.show.invoices.index', { order }),
                            )
                        "
                    >
                        <v-icon>arrow_back</v-icon>
                    </v-btn>
                    <v-toolbar-title>
                        {{ $t('order.invoices.back-to-invoices') }}
                    </v-toolbar-title>

                    <v-spacer />
                    <PreviewButton :invoice="invoice" />
                    <v-btn
                        v-if="invoice.status.is_draft"
                        color="info"
                        :disabled="!invoice.status.is_draft"
                        @click="confirmSendToClient"
                    >
                        <v-icon left>email</v-icon>
                        {{ $t('invoices.buttons.send-to-client') }}
                    </v-btn>
                    <v-btn
                        v-if="invoice.is_paid"
                        color="success"
                        depressed
                    >
                        <v-icon left>done_outline</v-icon>
                        {{ $t('invoices.labels.paid') }}
                    </v-btn>
                    <v-btn
                        v-if="!invoice.status.is_draft && !invoice.is_paid"
                        color="blue-info"
                        depressed
                        :disabled="invoice.is_paid"
                        class="white--text"
                        @click="
                            () =>
                                paymentFormEmitter.emit(
                                    'invoice.payments.form.show',
                                )
                        "
                    >
                        <v-icon left>payments</v-icon>
                        {{ $t('invoices.buttons.add_payment') }}
                    </v-btn>
                    <v-btn
                        v-if="!invoice.is_paid"
                        color="primary"
                        :disabled="
                            invoice.status.is_draft || invoice.status.is_paid
                        "
                        @click="confirmMarkingAsPaid"
                    >
                        <v-icon left>email</v-icon>
                        {{ $t('invoices.buttons.mark-as-paid') }}
                    </v-btn>
                </v-toolbar>
            </portal>

            <v-container
                grid-list-lg
                text-xs-center
                px-0
                class="!tw-max-w-full"
            >
                <v-layout
                    row
                    wrap
                    fill-height
                >
                    <v-flex
                        xs12
                        class="!tw-overflow-auto !tw-grow elevation-10"
                    >
                        <v-card flat>
                            <v-card-title primary-title>
                                <v-alert
                                    v-if="invoice.is_paid"
                                    :value="invoice.is_paid"
                                    color="success"
                                    class="tw-w-full tw-justify-between tw-text-left"
                                >
                                    <v-icon color="white">done_all</v-icon>
                                    {{ $t('invoices.message.mark-as-paid') }}
                                </v-alert>
                                <v-alert
                                    :value="invoice.status.is_draft"
                                    color="grey lighten-1"
                                    class="tw-w-full tw-justify-between tw-text-left"
                                >
                                    <v-icon color="white">edit_note</v-icon>
                                    {{ $t('invoices.labels.draft') }}
                                </v-alert>
                                <v-alert
                                    v-if="warnEmployeeRate"
                                    :value="warnEmployeeRate"
                                    color="warning"
                                    class="tw-w-full tw-justify-between tw-text-left"
                                >
                                    <v-icon color="white">done_all</v-icon>
                                    {{
                                        $t(
                                            'invoices.items.messages.employee_rate_not_set',
                                        )
                                    }}
                                    <Link
                                        :href="
                                            $route('account.employees.index')
                                        "
                                    >
                                        {{
                                            $t(
                                                'invoices.items.messages.set_employee_rate_link',
                                            )
                                        }}
                                    </Link>
                                </v-alert>
                            </v-card-title>
                            <v-card-text>
                                <v-container
                                    grid-list-md
                                    fluid
                                    pa-2
                                >
                                    <v-layout
                                        row
                                        wrap
                                    >
                                        <v-flex
                                            xs7
                                            class="tw-text-lg tw-text-left"
                                        >
                                            <div class="tw-font-semibold">
                                                {{
                                                    $t(
                                                        'invoices.labels.bill-to',
                                                    )
                                                }}:
                                            </div>
                                            <div>{{ client?.name }}</div>
                                            <div>
                                                <v-img
                                                    :src="client?.photo"
                                                    contain
                                                    class="grey lighten-2 mr-2"
                                                    height="50"
                                                    width="50"
                                                />
                                            </div>
                                            <!-- eslint-disable-next-line vue/no-v-html -->
                                            <div
                                                v-html="
                                                    careOf ??
                                                    client?.address.full
                                                "
                                            />
                                            <div>
                                                <v-label>
                                                    {{ $t('labels.order-no') }}
                                                </v-label>
                                                <a
                                                    :href="
                                                        $route('orders.show', {
                                                            order,
                                                        })
                                                    "
                                                    target="_blank"
                                                    class="tw-text-info tw-cursor-pointer hover:tw-underline"
                                                >
                                                    {{
                                                        invoice?.order
                                                            .order_number
                                                    }}
                                                </a>
                                            </div>
                                        </v-flex>
                                        <v-flex xs5>
                                            <div
                                                class="tw-grid tw-grid-cols-2 tw-text-right invoice-details"
                                            >
                                                <v-label class="">
                                                    {{
                                                        $t(
                                                            'invoices.form.invoice-no',
                                                        )
                                                    }}
                                                </v-label>
                                                <div
                                                    class="tw-text-lg tw-text-black tw-text-left tw-pl-4"
                                                >
                                                    {{ invoice?.internal_id }}
                                                </div>
                                                <v-label>
                                                    {{
                                                        $t(
                                                            'invoices.labels.invoice-date',
                                                        )
                                                    }}
                                                </v-label>
                                                <div
                                                    class="tw-text-lg tw-text-black tw-text-left tw-pl-4"
                                                >
                                                    {{
                                                        useDateFormat(
                                                            invoice?.send_date,
                                                        )
                                                    }}
                                                </div>
                                                <v-label>
                                                    {{
                                                        $t(
                                                            'invoices.form.external_id',
                                                        )
                                                    }}
                                                </v-label>
                                                <div
                                                    class="tw-text-lg tw-text-black tw-text-left tw-pl-4"
                                                >
                                                    {{
                                                        invoice?.external_id ??
                                                        '--'
                                                    }}
                                                </div>
                                                <v-label>
                                                    {{
                                                        $t(
                                                            'invoices.form.delivery_date',
                                                        )
                                                    }}
                                                </v-label>
                                                <div
                                                    class="tw-text-lg tw-text-black tw-text-left tw-pl-4"
                                                >
                                                    {{
                                                        useDateFormat(
                                                            invoice?.delivery_date,
                                                        )
                                                    }}
                                                </div>
                                                <v-label>
                                                    {{
                                                        $t(
                                                            'invoices.tax.labels.vat-identification-number',
                                                        )
                                                    }}
                                                </v-label>
                                                <div
                                                    class="tw-text-lg tw-text-black tw-text-left tw-pl-4"
                                                >
                                                    {{
                                                        invoice?.company
                                                            ?.vat_identification_number ??
                                                        '--'
                                                    }}
                                                </div>
                                                <v-label>
                                                    {{
                                                        $t(
                                                            'invoices.tax.labels.tax-number',
                                                        )
                                                    }}
                                                </v-label>
                                                <div
                                                    class="tw-text-lg tw-text-black tw-text-left tw-pl-4"
                                                >
                                                    {{
                                                        invoice?.company
                                                            ?.tax_number ?? '--'
                                                    }}
                                                </div>
                                                <!-- <v-label>
                                                    {{
                                                        $t(
                                                            'invoices.form.invoice-type',
                                                        )
                                                    }}
                                                </v-label>
                                                <div
                                                    class="tw-text-lg tw-text-black tw-text-left tw-pl-4"
                                                >
                                                    {{ invoice?.type?.name }}
                                                </div> -->
                                            </div>
                                        </v-flex>
                                        <v-flex
                                            xs12
                                            class="tw-flex tw-items-center"
                                        >
                                            <div
                                                class="tw-text-lg tw-font-semibold"
                                            >
                                                {{
                                                    $t('invoices.labels.items')
                                                }}:
                                            </div>
                                        </v-flex>
                                        <v-flex xs12>
                                            <InvoiceItems
                                                v-if="invoice?.id"
                                                :items="items"
                                                :currency="invoice.currency"
                                                card-text-class="px-0"
                                                :show-actions="
                                                    itemsCanBeUpdated
                                                "
                                                :show-update-quantity="
                                                    itemsCanBeUpdated
                                                "
                                                :subtotal="invoice.subtotal"
                                                :total="invoice.total"
                                                :taxes="invoice.taxes"
                                                :tax-editable="
                                                    authCan(
                                                        'can_manage_taxes',
                                                    ) && invoiceCanBeUpdated
                                                "
                                                :deducted-subtotal="
                                                    invoice.subtotal_after_deduction
                                                "
                                                @click:add-item="
                                                    (invoice) =>
                                                        invoiceItemFormRef.show(
                                                            invoice,
                                                        )
                                                "
                                                @click:remove-tax="
                                                    (tax) =>
                                                        confirmRemovingTax(tax)
                                                "
                                                @click:add-tax="
                                                    () =>
                                                        invoiceTaxFormRef.show(
                                                            invoice,
                                                        )
                                                "
                                            />
                                        </v-flex>
                                        <v-flex
                                            xs12
                                            mt-4
                                        >
                                            <div
                                                class="tw-text-lg tw-font-semibold tw-text-left"
                                            >
                                                {{ $t('labels.documents') }}:
                                            </div>
                                        </v-flex>
                                        <v-flex xs12>
                                            <v-container
                                                grid-list-sm
                                                fluid
                                                pt-0
                                                px-0
                                                mt-1
                                            >
                                                <InvoiceDocuments
                                                    :documents="documents"
                                                    :can-be-edited="
                                                        authCanUpdateDocuments
                                                    "
                                                />
                                            </v-container>
                                        </v-flex>
                                        <v-flex
                                            xs12
                                            mt-4
                                        >
                                            <div
                                                class="tw-text-lg tw-font-semibold tw-text-left"
                                            >
                                                {{ $t('invoices.form.notes') }}:
                                            </div>
                                        </v-flex>
                                        <v-flex xs12>
                                            <v-textarea
                                                readonly
                                                hide-details
                                                outline
                                                :value="invoice.notes"
                                            />
                                        </v-flex>

                                        <!-- Invoice Payments -->
                                        <div
                                            v-if="
                                                authCan('can_view_payments') ||
                                                invoice.is_draft
                                            "
                                            class="w-full"
                                        >
                                            <v-flex
                                                xs12
                                                mt-4
                                            >
                                                <InvoicePayments
                                                    :invoice="invoice"
                                                    :payments="payments"
                                                    :can-manage-payments="
                                                        authCan(
                                                            'can_manage_payments',
                                                        )
                                                    "
                                                    :can-view-payments="
                                                        authCan(
                                                            'can_view_payments',
                                                        )
                                                    "
                                                    :payments-summary="
                                                        paymentsSummary
                                                    "
                                                />
                                            </v-flex>
                                        </div>
                                        <!-- End of Invoice Payments -->
                                    </v-layout>
                                </v-container>
                            </v-card-text>
                            <v-divider />
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-container>
            <InvoiceItemForm ref="invoiceItemFormRef" />
        </div>
    </InvoicifyContainer>
</template>
<style scoped lang="scss">
.invoice-details {
    .v-label {
        @apply tw-flex tw-items-center tw-justify-end;

        &::after {
            content: ':';
            margin-left: 0.5rem;
        }
    }
}
</style>
