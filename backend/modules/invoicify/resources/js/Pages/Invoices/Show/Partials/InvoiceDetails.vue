<script setup>
import { usePage } from '@inertiajs/vue2'
import { computed } from 'vue'
import { useDateFormat } from '~Shared/Composables/useDayJs'
import useGlobalVariables from '~Shared/Composables/useGlobalVariables'
import { getPageProps } from '~Shared/Composables/useUtils'

const { $t } = useGlobalVariables()

const { invoice } = usePage().props

const invoiceIsEditable = computed(() =>
    getPageProps('permissions.invoice_is_editable'),
)
</script>

<template>
    <v-flex
        xs5
        class="tw-flex tw-justify-end"
    >
        <div class="tw-grid tw-grid-cols-2 invoice-details">
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
                {{ useDateFormat(invoice?.delivery_date) }}
            </div>
            <v-label>
                {{ $t('invoices.labels.payment-date') }}
            </v-label>
            <div class="tw-flex tw-items-center tw-justify-between">
                <div class="tw-text-lg tw-text-black">
                    {{ useDateFormat(invoice?.payment_date) }}
                </div>
                <v-btn
                    v-if="invoiceIsEditable"
                    v-tooltip="$t('invoices.labels.update-payment-date')"
                    flat
                    icon
                    color="info"
                    small
                    @click="invoicePaymentDateFormRef.show(invoice)"
                >
                    <v-icon small>edit</v-icon>
                </v-btn>
            </div>
        </div>
    </v-flex>
</template>
