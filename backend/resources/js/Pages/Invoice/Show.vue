<script setup>
import SubmitButton from '@/Components/Forms/SubmitButton.vue'
import { authCan } from '@/Composables/useAuth'
import { useDateFormat } from '@/Composables/useDayJs'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { getPageProps } from '@/Composables/useUtils'
import { router, useForm } from '@inertiajs/vue2'
import { get } from '@vueuse/core'
import { computed, provide, ref, toRef } from 'vue'
import AddPaymentButton from '~Invoicify/Components/Actions/AddPaymentButton.vue'
import DeleteInvoiceButton from '~Invoicify/Components/Actions/DeleteInvoiceButton.vue'
import PreviewButton from '~Invoicify/Components/Actions/PreviewButton.vue'
import SendToClientButton from '~Invoicify/Components/Actions/SendToClientButton.vue'
import InvoicifyContainer from '~Invoicify/Components/InvoicifyContainer.vue'
import DeductionOptions from './Components/DeductionOptions.vue'
import InvoiceDocuments from './Components/InvoiceDocuments.vue'
import InvoiceItemForm from './Components/InvoiceItemForm.vue'
import InvoiceItems from './Components/InvoiceItems.vue'
import InvoicePaymentDateForm from './Components/InvoicePaymentDateForm.vue'
import InvoicePayments from './Components/InvoicePayments.vue'
import InvoiceTaxForm from './Components/InvoiceTaxForm.vue'
import ManualItemForm from './Components/ManualItemForm.vue'

const props = defineProps({
    invoice: {
        required: true,
        type: Object,
    },
    invoiceSummary: {
        required: false,
        type: Object,
        default: () => ({}),
    },
    items: {
        required: true,
        type: Array,
    },
    documents: {
        required: false,
        type: Array,
        default: () => [],
    },
    permissions: {
        required: true,
        type: Object,
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

const { $t, $confirm, $route } = useGlobalVariables()
// const paymentFormEmitter = useEventBus('invoice.payments.form.show')

const invoice = toRef(props, 'invoice')

const invoiceItemFormRef = ref(null)
const invoiceDeductionRef = ref(null)
const invoiceTaxFormRef = ref(null)
const manualItemFormRef = ref(null)
const invoicePaymentDateFormRef = ref(null)
const isFocusedOnNotes = ref(false)

const form = useForm({
    notes: invoice.value.notes,
})

const client = computed(() => invoice.value.client)
const invoiceIsEditable = computed(() =>
    getPageProps('permissions.invoice_is_editable'),
)

const invoiceNotesWasChanged = computed(() => invoice.value.notes != form.notes)

function updateInvoiceNotes() {
    form.patch(
        window.route('invoices.update', {
            invoice: get(invoice, 'id'),
        }),
        {
            preserveScroll: true,
            preserveState: true,
            only: ['flash', 'invoice'],
        },
    )
}

// function confirmMarkingAsPaid() {
//     $confirm({
//         title: $t('labels.warning'),
//         question: $t('invoices.message.confirm_marking_as_paid'),
//         type: 'warning',
//         confirm: () => {
//             router.post(
//                 window.route('invoices.mark_as_paid', {
//                     invoice: invoice.value,
//                 }),
//                 {
//                     preserveState: true,
//                     preserveScroll: true,
//                     only: ['invoice', 'payments', 'paymentsSummary'],
//                     onSuccess: () => {
//                         this.fetch()
//                     },
//                 },
//             )
//         },
//     })
// }

// function confirmSendToClient() {
//     $confirm({
//         title: $t('labels.warning'),
//         question: $t('invoices.message.confirm_send_to_client'),
//         type: 'info',
//         confirm: () => {
//             router.post(
//                 window.route('invoices.send_to_client', {
//                     invoice: invoice.value,
//                 }),
//                 {
//                     preserveState: true,
//                     preserveScroll: true,
//                     only: ['invoice'],
//                     onSuccess: () => {
//                         this.fetch()
//                     },
//                 },
//             )
//         },
//     })
// }

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

// function confirmDeleting() {
//     const invoice = props.invoice
//     $confirm({
//         title: $t('labels.delete'),
//         question: $t('invoices.message.confirm_deleting'),
//         type: 'delete',
//         confirm: () => {
//             router.delete(
//                 $route('invoices.destroy', {
//                     invoice,
//                 }),
//                 {
//                     preserveState: true,
//                     preserveScroll: true,
//                     onSuccess: () => {
//                         router.get($route('invoices.index'))
//                     },
//                 },
//             )
//         },
//     })
// }

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
const addTax = () => invoiceTaxFormRef.value.show(props.invoice)

provide('addTax', addTax)
provide('confirmRemovingTax', confirmRemovingTax)
</script>
<template>
    <InvoicifyContainer :invoice="invoice">
        <ManualItemForm
            ref="manualItemFormRef"
            :invoice="invoice"
            @refresh="
                router.reload({ only: ['invoice', 'items', 'invoiceSummary'] })
            "
        />
        <InvoiceTaxForm ref="invoiceTaxFormRef" />
        <InvoicePaymentDateForm ref="invoicePaymentDateFormRef" />

        <v-toolbar
            color="white"
            flat
        >
            <v-toolbar-title class="title tw-flex tw-items-center">
                <div class="md:tw-text-xl tw-text-lg">
                    <v-btn
                        icon
                        @click="$inertia.get($route('invoices.index'))"
                    >
                        <v-icon>arrow_back</v-icon>
                    </v-btn>
                    {{ $t('invoices.labels.invoice-details') }}
                </div>
            </v-toolbar-title>
            <v-spacer />

            <PreviewButton :invoice="invoice" />

            <!--
            @deprecated
            <v-btn
                v-if="invoice.status.is_draft"
                color="info"
                :disabled="!invoice.status.is_draft"
                depressed
                @click="confirmSendToClient"
            >
                <v-icon left>email</v-icon>
                {{ $t('invoices.buttons.send-to-client') }}
            </v-btn> -->
            <SendToClientButton />
            <AddPaymentButton />

            <!--
            @deprecated
            <v-btn
                v-if="!invoice.status.is_draft && !invoice.is_paid"
                color="blue-info"
                depressed
                :disabled="invoice.is_paid"
                class="white--text"
                @click="
                    () => paymentFormEmitter.emit('invoice.payments.form.show')
                "
            >
                <v-icon left>payments</v-icon>
                {{ $t('invoices.buttons.add_payment') }}
            </v-btn> -->

            <v-btn
                v-if="invoice.is_paid"
                color="success"
                depressed
            >
                <v-icon left>done_outline</v-icon>
                {{ $t('invoices.labels.paid') }}
            </v-btn>

            <DeleteInvoiceButton />

            <!--
            @deprecated
            <v-btn
                v-if="!invoice.is_paid"
                color="primary"
                :disabled="invoice.status.is_draft || invoice.status.is_paid"
                @click="confirmMarkingAsPaid"
            >
                <v-icon left>check_circle</v-icon>
                {{ $t('invoices.buttons.mark-as-paid') }}
            </v-btn> -->

            <!--
            @deprecated
            <v-btn
                v-if="
                    authCan('can_delete_invoice') &&
                    authCan('invoice_is_editable')
                "
                color="error"
                @click="confirmDeleting"
            >
                <v-icon left>delete</v-icon>
                {{ $t('buttons.delete') }}
            </v-btn> -->
        </v-toolbar>
        <v-divider />
        <v-container fluid>
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
                                :value="invoice.is_paid"
                                type="success"
                                class="tw-w-full"
                                icon="price_check"
                            >
                                {{ $t('invoices.message.mark-as-paid') }}
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
                                        class="tw-text-lg"
                                    >
                                        <div class="tw-font-semibold">
                                            {{ $t('invoices.labels.bill-to') }}:
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
                                        <!-- eslint-disable vue/no-v-html -->
                                        <div
                                            v-html="
                                                careOf ?? client?.address.full
                                            "
                                        />
                                        <!-- eslint-enable vue/no-v-html -->

                                        <div>
                                            <v-label>
                                                {{ $t('labels.order-no') }}
                                            </v-label>
                                            <a
                                                :href="
                                                    $route('orders.show', {
                                                        order: invoice?.order
                                                            .id,
                                                    })
                                                "
                                                target="_blank"
                                                class="tw-text-info tw-cursor-pointer hover:tw-underline"
                                            >
                                                {{
                                                    invoice?.order.order_number
                                                }}
                                            </a>
                                        </div>
                                        <div class="tw-flex tw-items-center">
                                            <v-label>
                                                {{
                                                    $t(
                                                        'invoices.preview.construction-site',
                                                    )
                                                }}:
                                            </v-label>
                                            <div class="ml-2 tw-mt-[1px]">
                                                {{
                                                    invoice?.construction_site
                                                        ?.short_full_address ??
                                                    '--'
                                                }}
                                            </div>
                                        </div>
                                    </v-flex>
                                    <v-flex
                                        xs5
                                        class="tw-flex tw-justify-end"
                                    >
                                        <div
                                            class="tw-grid tw-grid-cols-2 invoice-details"
                                        >
                                            <v-label>
                                                {{
                                                    $t(
                                                        'invoices.form.invoice-no',
                                                    )
                                                }}
                                            </v-label>
                                            <div
                                                class="tw-text-lg tw-text-black"
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
                                                class="tw-text-lg tw-text-black"
                                            >
                                                {{
                                                    useDateFormat(
                                                        invoice?.created_at,
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
                                                class="tw-text-lg tw-text-black"
                                            >
                                                {{ invoice?.external_id }}
                                            </div>
                                            <v-label>
                                                {{
                                                    $t(
                                                        'invoices.form.delivery_date',
                                                    )
                                                }}
                                            </v-label>
                                            <div
                                                class="tw-text-lg tw-text-black"
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
                                                        'invoices.labels.payment-date',
                                                    )
                                                }}
                                            </v-label>
                                            <div
                                                class="tw-flex tw-items-center tw-justify-between"
                                            >
                                                <div
                                                    class="tw-text-lg tw-text-black"
                                                >
                                                    {{
                                                        useDateFormat(
                                                            invoice?.payment_date,
                                                        )
                                                    }}
                                                </div>
                                                <v-btn
                                                    v-if="invoiceIsEditable"
                                                    v-tooltip="
                                                        $t(
                                                            'invoices.labels.update-payment-date',
                                                        )
                                                    "
                                                    flat
                                                    icon
                                                    color="info"
                                                    small
                                                    @click="
                                                        invoicePaymentDateFormRef.show(
                                                            invoice,
                                                        )
                                                    "
                                                >
                                                    <v-icon small>edit</v-icon>
                                                </v-btn>
                                            </div>
                                        </div>
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        class="tw-flex tw-items-center"
                                    >
                                        <div
                                            class="tw-text-lg tw-font-semibold"
                                        >
                                            {{ $t('invoices.labels.items') }}:
                                        </div>
                                        <v-spacer />
                                        <v-btn
                                            v-if="invoice.can_be_edited"
                                            color="purple"
                                            class="white--text"
                                            @click="manualItemFormRef.show()"
                                        >
                                            {{
                                                $t(
                                                    'invoices.buttons.add-manual-item',
                                                )
                                            }}
                                        </v-btn>
                                        <v-btn
                                            v-if="invoice.can_be_edited"
                                            color="default"
                                            @click="invoiceDeductionRef.show()"
                                        >
                                            {{
                                                $t(
                                                    'invoices.labels.add-deduction',
                                                )
                                            }}
                                        </v-btn>
                                        <v-btn
                                            v-if="invoice.can_be_edited"
                                            color="info"
                                            @click="invoiceItemFormRef.show()"
                                        >
                                            {{ $t('invoices.labels.add-item') }}
                                        </v-btn>
                                    </v-flex>

                                    <v-flex xs12>
                                        <InvoiceItems
                                            v-if="invoice?.id"
                                            :items="items"
                                            :invoice="invoice"
                                            :show-actions="
                                                authCan(
                                                    'invoice_is_editable',
                                                ) &&
                                                authCan('can_update_items') &&
                                                invoiceIsEditable
                                            "
                                            :show-update-quantity="
                                                invoiceIsEditable
                                            "
                                            :tax-editable="
                                                authCan('can_manage_taxes') &&
                                                invoiceIsEditable
                                            "
                                            card-text-class="px-0"
                                            @click:add-item="
                                                (invoice) =>
                                                    $emit(
                                                        'click:add-item',
                                                        invoice,
                                                    )
                                            "
                                            @click:remove-tax="
                                                (tax) => confirmRemovingTax(tax)
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
                                            class="tw-text-lg tw-font-semibold"
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
                                                    invoice?.can_be_edited
                                                "
                                            />
                                        </v-container>
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        mt-4
                                    >
                                        <div
                                            class="tw-text-lg tw-font-semibold"
                                        >
                                            {{ $t('invoices.form.notes') }}:
                                        </div>
                                    </v-flex>
                                    <v-flex xs12>
                                        <v-textarea
                                            v-model="form.notes"
                                            :disabled="
                                                form.processing ||
                                                !invoice.can_be_edited
                                            "
                                            :loading="form.processing"
                                            :placeholder="
                                                $t('invoices.form.add-notes')
                                            "
                                            :error-messages="form.errors.notes"
                                            :hide-details="!form.errors.notes"
                                            outline
                                            height="200"
                                            @focusin="
                                                () => {
                                                    if (!invoice.can_be_edited)
                                                        return

                                                    isFocusedOnNotes = true
                                                }
                                            "
                                            @focusout="
                                                () => {
                                                    if (!invoice.can_be_edited)
                                                        return

                                                    isFocusedOnNotes = false
                                                }
                                            "
                                        />
                                        <div class="tw-text-right">
                                            <SubmitButton
                                                v-show="
                                                    isFocusedOnNotes ||
                                                    invoiceNotesWasChanged
                                                "
                                                :is-processing="form.processing"
                                                class="mx-0"
                                                @click="updateInvoiceNotes"
                                            >
                                                {{ $t('buttons.save-changes') }}
                                            </SubmitButton>
                                        </div>
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
                                                    authCan('can_view_payments')
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
        <DeductionOptions
            ref="invoiceDeductionRef"
            :invoice="invoice"
            :permissions="permissions"
        />
    </InvoicifyContainer>
</template>
<style scoped lang="scss">
.invoice-details {
    .v-label {
        @apply tw-flex tw-items-center tw-justify-end tw-pr-2;

        &::after {
            content: ':';
            margin-left: 0.5rem;
        }
    }
}
</style>
