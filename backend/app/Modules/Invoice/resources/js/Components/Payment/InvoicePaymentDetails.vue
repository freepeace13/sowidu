<script setup>
import { useDateFormat } from '@/Composables/useDayJs'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { ref } from 'vue'

defineExpose({ show })

defineProps({
    invoice: {
        required: false,
        type: Object,
    },
})

const { $t } = useGlobalVariables()

const isShow = ref(false)
const payment = ref(null)

function show(invoicePayment) {
    if (!invoicePayment) {
        return
    }

    payment.value = invoicePayment
    isShow.value = true
}

function close() {
    payment.value = null
    isShow.value = false
}
</script>
<template>
    <v-dialog
        v-model="isShow"
        width="600"
        persistent
        lazy
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{ $t('invoices.payments.labels.record_payment') }}
                </v-toolbar-title>
                <v-spacer />
                <v-btn
                    icon
                    @click="close"
                >
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-divider />
            <v-card-text>
                <v-container
                    grid-list-lg
                    fluid
                    pa-2
                    class="invoice-payment-details"
                >
                    <v-layout
                        row
                        wrap
                    >
                        <v-flex xs8>
                            <v-label>
                                {{ $t('invoices.payments.labels.payer_name') }}:
                            </v-label>
                            <div class="tw-text-lg">
                                {{ payment?.payer_name }}
                            </div>
                        </v-flex>
                        <v-flex xs4>
                            <v-label>
                                {{
                                    $t('invoices.payments.labels.payment_date')
                                }}:
                            </v-label>
                            <div class="tw-text-lg">
                                {{ useDateFormat(payment?.created_at) }}
                            </div>
                        </v-flex>

                        <v-flex xs8>
                            <v-label>
                                {{ $t('invoices.payments.labels.amount') }}:
                            </v-label>
                            <div class="tw-text-lg">
                                {{ payment?.amount_formatted }}
                            </div>
                        </v-flex>

                        <v-flex xs4>
                            <v-label>
                                {{
                                    $t(
                                        'invoices.payments.labels.payment_method',
                                    )
                                }}:
                            </v-label>
                            <div class="tw-text-lg">
                                <v-chip
                                    :color="payment?.method_color"
                                    label
                                >
                                    {{ payment?.method_label }}
                                </v-chip>
                            </div>
                        </v-flex>
                        <v-flex
                            v-show="payment?.reference_number"
                            xs12
                        >
                            <v-label>
                                {{
                                    $t(
                                        'invoices.payments.labels.reference_number',
                                    )
                                }}:
                            </v-label>
                            <div class="tw-text-lg">
                                {{ payment?.reference_number }}
                            </div>
                        </v-flex>
                        <v-flex
                            xs12
                            class="tw-flex tw-flex-col tw-gap-y-3"
                        >
                            <VLabel>
                                {{ $t('invoices.payments.labels.notes') }}:
                            </VLabel>
                            <div class="tw-text-lg tw-text-black">
                                {{ payment?.notes ?? '--' }}
                            </div>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>

            <v-divider />

            <v-card-actions class="px-4 py-4">
                <v-spacer />
                <v-btn
                    outline
                    depressed
                    @click="close"
                >
                    {{ $t('buttons.close') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<style lang="scss">
.invoice-payment-details {
    .v-label {
        font-size: 13px !important;
    }
}
</style>
