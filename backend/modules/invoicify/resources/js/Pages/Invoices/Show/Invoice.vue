<script setup>
import { useForm, usePage } from '@inertiajs/vue2'
import { computed, toRef } from 'vue'
import { authCan, canAll } from '~Shared/Composables/useAuth'
import useGlobalVariables from '~Shared/Composables/useGlobalVariables'
import { getPageProps } from '~Shared/Composables/useUtils'

const { $t, $route } = useGlobalVariables()
const { items, documents, permissions, paymentsSummary, ...page } = usePage()

const form = useForm({
    notes: invoice.value.notes,
})

const invoice = toRef(page.props, 'invoice')
const client = computed(() => invoice.value.client)

const invoiceIsEditable = computed(() =>
    getPageProps('permissions.invoice_is_editable'),
)
const invoiceNotesWasChanged = computed(() => invoice.value.notes != form.notes)
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
</script>

<template>
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
                                <div v-html="careOf ?? client?.address.full" />
                                <!-- eslint-enable vue/no-v-html -->

                                <div>
                                    <v-label>
                                        {{ $t('labels.order-no') }}
                                    </v-label>
                                    <a
                                        :href="
                                            $route('orders.show', {
                                                order: invoice?.order.id,
                                            })
                                        "
                                        target="_blank"
                                        class="tw-text-info tw-cursor-pointer hover:tw-underline"
                                    >
                                        {{ invoice?.order.order_number }}
                                    </a>
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
                                        {{ $t('invoices.form.invoice-no') }}
                                    </v-label>
                                    <div class="tw-text-lg tw-text-black">
                                        {{ invoice?.internal_id }}
                                    </div>
                                    <v-label>
                                        {{ $t('invoices.labels.invoice-date') }}
                                    </v-label>
                                    <div class="tw-text-lg tw-text-black">
                                        {{ useDateFormat(invoice?.created_at) }}
                                    </div>
                                    <v-label>
                                        {{ $t('invoices.form.external_id') }}
                                    </v-label>
                                    <div class="tw-text-lg tw-text-black">
                                        {{ invoice?.external_id }}
                                    </div>
                                    <v-label>
                                        {{ $t('invoices.form.delivery_date') }}
                                    </v-label>
                                    <div class="tw-text-lg tw-text-black">
                                        {{
                                            useDateFormat(
                                                invoice?.delivery_date,
                                            )
                                        }}
                                    </div>
                                    <v-label>
                                        {{ $t('invoices.labels.payment-date') }}
                                    </v-label>
                                    <div
                                        class="tw-flex tw-items-center tw-justify-between"
                                    >
                                        <div class="tw-text-lg tw-text-black">
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
                                <div class="tw-text-lg tw-font-semibold">
                                    {{ $t('invoices.labels.items') }}:
                                </div>
                                <v-spacer />
                                <v-btn
                                    v-if="invoice.can_be_edited"
                                    color="purple"
                                    class="white--text"
                                    @click="manualItemFormRef.show()"
                                >
                                    {{ $t('invoices.buttons.add-manual-item') }}
                                </v-btn>
                                <v-btn
                                    v-if="invoice.can_be_edited"
                                    color="default"
                                    @click="invoiceDeductionRef.show()"
                                >
                                    {{ $t('invoices.labels.add-deduction') }}
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
                                        canAll([
                                            'invoice_is_editable',
                                            'can_update_items',
                                        ]) && invoiceIsEditable
                                    "
                                    :show-update-quantity="invoiceIsEditable"
                                    :tax-editable="
                                        authCan('can_manage_taxes') &&
                                        invoiceIsEditable
                                    "
                                    card-text-class="px-0"
                                    @click:add-item="
                                        (invoice) =>
                                            $emit('click:add-item', invoice)
                                    "
                                    @click:remove-tax="
                                        (tax) => confirmRemovingTax(tax)
                                    "
                                    @click:add-tax="
                                        () => invoiceTaxFormRef.show(invoice)
                                    "
                                />
                            </v-flex>

                            <v-flex
                                xs12
                                mt-4
                            >
                                <div class="tw-text-lg tw-font-semibold">
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
                                        :can-be-edited="invoice?.can_be_edited"
                                    />
                                </v-container>
                            </v-flex>
                            <v-flex
                                xs12
                                mt-4
                            >
                                <div class="tw-text-lg tw-font-semibold">
                                    {{ $t('invoices.form.notes') }}:
                                </div>
                            </v-flex>
                            <v-flex xs12>
                                <v-textarea
                                    v-model="form.notes"
                                    outline
                                    height="200"
                                    :disabled="
                                        form.processing ||
                                        !invoice.can_be_edited
                                    "
                                    :loading="form.processing"
                                    :placeholder="$t('invoices.form.add-notes')"
                                    :error-messages="form.errors.notes"
                                    :hide-details="!form.errors.notes"
                                    @focusin="
                                        () =>
                                            invoice.can_be_edited &&
                                            (isFocusedOnNotes = true)
                                    "
                                    @focusout="
                                        () =>
                                            invoice.can_be_edited &&
                                            (isFocusedOnNotes = false)
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
                                            authCan('can_manage_payments')
                                        "
                                        :can-view-payments="
                                            authCan('can_view_payments')
                                        "
                                        :payments-summary="paymentsSummary"
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
</template>
