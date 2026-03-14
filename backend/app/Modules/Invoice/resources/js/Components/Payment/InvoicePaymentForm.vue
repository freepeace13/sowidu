<script setup>
import SubmitButton from '@/Components/Forms/SubmitButton.vue'
import { useDateNow } from '@/Composables/useDayJs'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { useForm } from '@inertiajs/vue2'
import { ref } from 'vue'

defineExpose({ show })

const props = defineProps({
    invoice: {
        required: false,
        type: Object,
    },
})

const { $route, $t } = useGlobalVariables()

const invoiceId = ref(null)

const form = useForm({
    payment_date: null,
    amount: null,
    method: null,
    reference_number: null,
    payer_name: null,
    notes: null,
})

const isShow = ref(false)
const invoicePayment = ref(null)

const paymentMethods = [
    {
        text: $t('invoices.payments.methods.cash'),
        value: 0,
    },
    {
        text: $t('invoices.payments.methods.credit_card'),
        value: 1,
    },
    {
        text: $t('invoices.payments.methods.bank_transfer'),
        value: 2,
    },
    {
        text: $t('invoices.payments.methods.check'),
        value: 3,
    },
    {
        text: $t('invoices.payments.methods.others'),
        value: 4,
    },
]

function show(payment, invoiceToUse = null) {
    invoiceId.value = invoiceToUse?.id ?? props.invoice.id

    const clientName = invoiceToUse?.client?.name ?? props.invoice.client.name
    form.payment_date = useDateNow()
    form.payer_name = clientName

    if (payment) {
        invoicePayment.value = payment
    }

    isShow.value = true
}

function close() {
    reset()
    isShow.value = false
}

function reset() {
    invoicePayment.value = null
    invoiceId.value = null
    form.reset()
}

function submit() {
    form.post(
        $route('invoices.payments.store', {
            invoice: invoiceId.value,
        }),
        {
            preserveScroll: true,
            preserveState: true,
            only: ['payments', 'invoice', 'paymentsSummary'],
            onSuccess: () => close(),
        },
    )
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
                >
                    <v-layout
                        row
                        wrap
                    >
                        <v-flex xs12>
                            <v-menu
                                :close-on-content-click="true"
                                lazy
                                transition="scale-transition"
                                offset-y
                                full-width
                                min-width="290px"
                            >
                                <template #activator="{ on }">
                                    <v-text-field
                                        :value="form.payment_date"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                        :error-messages="
                                            form.errors.payment_date
                                        "
                                        :hide-details="
                                            !form.errors.payment_date
                                        "
                                        :label="
                                            $t(
                                                'invoices.payments.labels.payment_date',
                                            )
                                        "
                                        required
                                        readonly
                                        color="primary"
                                        outline
                                        v-on="on"
                                    />
                                </template>
                                <v-date-picker
                                    v-model="form.payment_date"
                                    :loading="form.processing"
                                    :disabled="form.processing"
                                    scrollable
                                    reactive
                                    picker-date
                                />
                            </v-menu>
                        </v-flex>
                        <v-flex xs12>
                            <v-text-field
                                v-model="form.payer_name"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.payer_name"
                                :hide-details="!form.errors.payer_name"
                                :label="
                                    $t('invoices.payments.labels.payer_name')
                                "
                                color="primary"
                                outline
                            />
                        </v-flex>
                        <v-flex xs6>
                            <v-text-field
                                v-model="form.amount"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.amount"
                                :hide-details="!form.errors.amount"
                                :label="$t('invoices.payments.labels.amount')"
                                color="primary"
                                required
                                type="number"
                                class="required-input"
                                outline
                            />
                        </v-flex>
                        <v-flex xs6>
                            <v-select
                                v-model="form.method"
                                outline
                                full-width
                                :items="paymentMethods"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.method"
                                :hide-details="!form.errors.method"
                                :label="
                                    $t(
                                        'invoices.payments.labels.payment_method',
                                    )
                                "
                                class="required-input"
                                required
                            />
                        </v-flex>
                        <v-flex xs12>
                            <v-text-field
                                v-model="form.reference_number"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.reference_number"
                                :hide-details="!form.errors.reference_number"
                                :label="
                                    $t(
                                        'invoices.payments.labels.reference_number',
                                    )
                                "
                                color="primary"
                                outline
                            />
                        </v-flex>
                        <v-flex
                            xs12
                            class="tw-flex tw-flex-col tw-gap-y-3"
                        >
                            <v-textarea
                                v-model="form.notes"
                                outline
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.notes"
                                :hide-details="!form.errors.notes"
                                :label="$t('invoices.payments.labels.notes')"
                            />
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>

            <v-divider />

            <v-card-actions class="px-4 py-4">
                <v-btn
                    :disabled="form.processing"
                    :loading="form.processing"
                    outline
                    depressed
                    @click="close"
                >
                    {{ $t('buttons.cancel') }}
                </v-btn>
                <v-spacer />

                <SubmitButton
                    :loading="form.processing"
                    :disabled="form.processing"
                    @click="submit"
                >
                    {{ $t('buttons.save') }}
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
