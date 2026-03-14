<script setup>
import AddItemButton from '~Invoicify/Components/Actions/AddItemButton.vue'
import AddManualItemButton from '~Invoicify/Components/Actions/AddManualItemButton.vue'
import ShowInvoiceLayout from './Layout.vue'
import ClientInfo from './Partials/ClientInfo.vue'
import EditableNotes from './Partials/EditableNotes.vue'
import InvoiceDetails from './Partials/InvoiceDetails.vue'
import InvoiceItems from './Partials/InvoiceItems.vue'
import MarkedAsPaidAlert from './Partials/MarkedAsPaidAlert.vue'
import PaymentHistory from './Partials/PaymentHistory.vue'
</script>

<template>
    <ShowInvoiceLayout>
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
                    <MarkedAsPaidAlert />
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
                                <ClientInfo />
                                <InvoiceDetails />
                                <v-flex
                                    xs12
                                    class="tw-flex tw-items-center"
                                >
                                    <div class="tw-text-lg tw-font-semibold">
                                        {{ $t('invoices.labels.items') }}:
                                    </div>
                                    <v-spacer />
                                    <AddManualItemButton />
                                    <v-btn
                                        v-if="invoice.can_be_edited"
                                        color="default"
                                        @click="invoiceDeductionRef.show()"
                                    >
                                        {{
                                            $t('invoices.labels.add-deduction')
                                        }}
                                    </v-btn>
                                    <AddItemButton />
                                </v-flex>

                                <v-flex xs12>
                                    <InvoiceItems />
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
                                    <div class="tw-text-lg tw-font-semibold">
                                        {{ $t('invoices.form.notes') }}:
                                    </div>
                                </v-flex>
                                <EditableNotes />
                                <PaymentHistory />
                            </v-layout>
                        </v-container>
                    </v-card-text>
                    <v-divider />
                </v-card>
            </v-flex>
        </v-layout>
    </ShowInvoiceLayout>
</template>
